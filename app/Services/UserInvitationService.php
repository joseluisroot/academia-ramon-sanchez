<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\UserInvitationModel;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use DomainException;
use RuntimeException;
use Throwable;

final class UserInvitationService
{
    public function invite(User $actor, string $emailAddress, string $role): void
    {
        if (! $actor->can('users.invite')) {
            throw new DomainException('No tienes permiso para enviar invitaciones.');
        }

        $emailAddress = mb_strtolower(trim($emailAddress));

        if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL) === false) {
            throw new DomainException('Ingresa un correo electrónico válido.');
        }

        if (! InvitationPolicy::canInviteTo($role)) {
            throw new DomainException('Este perfil requiere autorización institucional especial.');
        }

        /** @var UserModel $users */
        $users = model(UserModel::class);

        if ($users->findByCredentials(['email' => $emailAddress]) !== null) {
            throw new DomainException('La cuenta ya existe; utiliza el proceso de soporte.');
        }

        /** @var UserInvitationModel $invitations */
        $invitations = model(UserInvitationModel::class);
        $now = date('Y-m-d H:i:s');

        if ($invitations->where('email', $emailAddress)
            ->where('status', 'pending')
            ->where('expires_at >', $now)->first() !== null) {
            throw new DomainException('Ya existe una invitación vigente para este correo.');
        }

        $mailConfig = config('Email');

        // Evitar marcar como "enviadas" invitaciones cuando SMTP no está listo.
        if ($mailConfig->protocol !== 'smtp'
            || $mailConfig->SMTPHost === ''
            || ! filter_var($mailConfig->fromEmail, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('El servicio SMTP institucional todavía no está configurado.');
        }

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + InvitationPolicy::HOURS_VALID * 3600);
        $link = site_url('activar-cuenta/' . $token);

        $database = db_connect();
        $database->transBegin();

        try {
            $id = $invitations->insert([
                'email' => $emailAddress,
                'role' => $role,
                'token_hash' => hash('sha256', $token),
                'status' => 'pending',
                'invited_by' => (int) $actor->id,
                'created_at' => $now,
                'expires_at' => $expires,
            ], true);

            if (! is_numeric($id)) {
                throw new RuntimeException('No fue posible registrar la invitación.');
            }

            (new AuditLogger())->record(
                (int) $actor->id,
                'user.invitation.created',
                'user_invitation',
                (string) $id,
                'Invitación administrativa',
                ['role' => $role, 'source' => 'portal'],
            );

            if ($database->transStatus() === false) {
                throw new RuntimeException('No fue posible guardar la invitación.');
            }

            // Enviar antes de confirmar para evitar persistir una invitación fallida.
            $mailer = service('email');
            $mailer->clear(true);
            $mailer->setFrom($mailConfig->fromEmail, $mailConfig->fromName ?: 'Academia Ramón Sánchez');
            $mailer->setTo($emailAddress);
            $mailer->setSubject('Invitación al portal de la Academia Ramón Sánchez');
            $mailer->setMailType('text');
            $mailer->setMessage(
                "Has sido invitado a activar tu cuenta en el portal de la Academia Ramón Sánchez.\n\n"
                . "Para establecer tu contraseña, abre este enlace:\n"
                . $link . "\n\n"
                . "El enlace vence en 48 horas y solo puede utilizarse una vez.\n"
                . "Si no esperabas esta invitación, ignora el mensaje.\n"
            );

            if (! $mailer->send(false)) {
                throw new RuntimeException('No fue posible enviar la invitación. Verifica SMTP.');
            }

            $database->transCommit();
        } catch (Throwable $exception) {
            $database->transRollback();
            throw $exception;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findValid(string $token): ?array
    {
        if (! InvitationPolicy::validToken($token)) {
            return null;
        }

        /** @var UserInvitationModel $invitations */
        $invitations = model(UserInvitationModel::class);

        return $invitations->where('token_hash', hash('sha256', $token))
            ->where('status', 'pending')
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->first();
    }

    public function activate(string $token, string $username, string $password, string $confirmation): void
    {
        $username = trim($username);

        if (! InvitationPolicy::validUsername($username)) {
            throw new DomainException('El usuario debe tener de 3 a 30 caracteres: letras, números o puntos.');
        }

        if (! InvitationPolicy::validPassword($password, $confirmation, $username)) {
            throw new DomainException('Usa una contraseña de 12 a 128 caracteres, con mayúscula, minúscula, número y símbolo, y confirma que coincida.');
        }

        $record = $this->findValid($token);

        if ($record === null) {
            throw new DomainException('La invitación es inválida, ya se utilizó o ha vencido.');
        }

        /** @var UserInvitationModel $invitations */
        $invitations = model(UserInvitationModel::class);
        /** @var UserModel $users */
        $users = model(UserModel::class);
        $database = db_connect();
        $database->transBegin();

        try {
            // Reclamo atómico: un mismo enlace no puede activar dos cuentas.
            $database->table('user_invitations')
                ->where('id', $record['id'])
                ->where('status', 'pending')
                ->where('expires_at >', date('Y-m-d H:i:s'))
                ->update(['status' => 'accepted', 'consumed_at' => date('Y-m-d H:i:s')]);

            if ($database->affectedRows() !== 1) {
                throw new DomainException('Esta invitación ya no está disponible.');
            }

            if ($users->findByCredentials(['email' => $record['email']]) !== null
                || $users->where('username', $username)->first() !== null) {
                throw new DomainException('La cuenta o nombre de usuario ya existe.');
            }

            if (! InvitationPolicy::canInviteTo((string) $record['role'])) {
                throw new DomainException('El tipo de cuenta no está autorizado.');
            }

            $user = $users->createNewUser([
                'username' => $username,
                'email' => (string) $record['email'],
                'password' => $password,
            ]);

            if (! $users->save($user)) {
                throw new RuntimeException('No se pudo crear la cuenta. Verifica los datos.');
            }

            $created = $users->findById((int) $users->getInsertID());

            if (! $created instanceof User) {
                throw new RuntimeException('No se pudo verificar la cuenta creada.');
            }

            $created->addGroup((string) $record['role']);
            $created->activate();

            (new AuditLogger())->record(
                (int) $record['invited_by'],
                'user.invitation.accepted',
                'user',
                (string) $created->id,
                'Activación de cuenta invitada',
                ['role' => (string) $record['role'], 'source' => 'invitation'],
            );

            if ($database->transStatus() === false) {
                throw new RuntimeException('No fue posible completar la activación.');
            }

            $database->transCommit();
        } catch (Throwable $exception) {
            $database->transRollback();
            throw $exception;
        }
    }
}
