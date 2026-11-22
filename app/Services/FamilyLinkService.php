<?php

declare(strict_types=1);

namespace App\Services;

use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use DomainException;
use RuntimeException;
use Throwable;

/**
 * Autoriza el acceso familiar solo tras verificación humana expresa.
 * Nunca infiere el parentesco de un correo, apellido o invitación.
 */
final class FamilyLinkService
{
    public function verify(User $actor, int $relationId, string $accountEmail, bool $confirmed): void
    {
        if (! $actor->can('family.links.verify')) {
            throw new DomainException('No tienes autorización para verificar relaciones familiares.');
        }

        if (! $confirmed || $relationId <= 0) {
            throw new DomainException('Debes confirmar la identidad y el vínculo familiar antes de continuar.');
        }

        $accountEmail = mb_strtolower(trim($accountEmail));

        if (filter_var($accountEmail, FILTER_VALIDATE_EMAIL) === false) {
            throw new DomainException('Introduce el correo de una cuenta activa de la academia.');
        }

        /** @var UserModel $users */
        $users = model(UserModel::class);
        $account = $users->findByCredentials(['email' => $accountEmail]);

        if (! $account instanceof User || ! $account->inGroup('guardian') || ! (bool) $account->active) {
            throw new DomainException('La cuenta de responsable no existe, no está activa o no tiene el rol familiar.');
        }

        $db = db_connect();
        $db->transBegin();

        try {
            $relation = $db->table('athlete_guardians ag')
                ->select('ag.id, ag.person_id, ag.verified_at, p.user_id')
                ->join('persons p', 'p.id = ag.person_id')
                ->where('ag.id', $relationId)
                ->where('p.status', 'active')
                ->get()
                ->getRowArray();

            if ($relation === null || $relation['verified_at'] !== null) {
                throw new DomainException('La relación no existe o ya está verificada.');
            }

            $existingAccountId = $relation['user_id'];

            if ($existingAccountId !== null && (int) $existingAccountId !== (int) $account->id) {
                throw new DomainException('Este adulto ya está vinculado a otra cuenta.');
            }

            if ($existingAccountId === null) {
                $db->table('persons')->where('id', (int) $relation['person_id'])
                    ->where('user_id IS NULL', null, false)
                    ->update(['user_id' => (int) $account->id]);

                if ($db->affectedRows() !== 1) {
                    throw new DomainException('El adulto fue vinculado por otra operación. Revisa el expediente.');
                }
            }

            $db->table('athlete_guardians')->where('id', $relationId)
                ->where('verified_at IS NULL', null, false)
                ->update([
                    'verified_by' => (int) $actor->id,
                    'verified_at' => date('Y-m-d H:i:s'),
                ]);

            if ($db->affectedRows() !== 1) {
                throw new DomainException('La relación ya fue verificada.');
            }

            (new AuditLogger())->record(
                (int) $actor->id,
                'family.link.verified',
                'athlete_guardian',
                (string) $relationId,
                'Identidad y parentesco comprobados administrativamente',
                ['result' => 'verified', 'source' => 'portal'],
            );

            if ($db->transStatus() === false) {
                throw new RuntimeException('No fue posible guardar la verificación.');
            }

            $db->transCommit();
        } catch (Throwable $exception) {
            $db->transRollback();

            throw $exception;
        }
    }
}
