<?php

declare(strict_types=1);

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use DomainException;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

/**
 * Primer alcance seguro de asignación de grupos ordinarios.
 *
 * Los accesos financieros, institucionales y de soporte requieren un
 * proceso de aprobación especial que no se habilita en esta pantalla.
 */
final class RoleAssignmentService
{
    public function __construct(
        private ?UserModel $users = null,
        private ?AuditLogger $audit = null,
        private ?BaseConnection $db = null,
    ) {
        $this->users ??= model(UserModel::class);
        $this->audit ??= new AuditLogger();
        $this->db ??= db_connect();
    }

    public function change(
        User $actor,
        int $targetId,
        string $group,
        string $operation,
        string $reason,
    ): void {
        if (! $actor->can('users.assign')) {
            throw new DomainException('No tienes autorización para administrar accesos.');
        }

        if ($targetId <= 0 || (int) $actor->id === $targetId) {
            throw new DomainException('No puedes modificar tu propia cuenta.');
        }

        if (! RoleAssignmentPolicy::canAssign($group)) {
            throw new DomainException('Este grupo requiere un procedimiento institucional especial.');
        }

        if (! in_array($operation, ['grant', 'revoke'], true)) {
            throw new InvalidArgumentException('Operación no válida.');
        }

        $reason = trim($reason);

        if (mb_strlen($reason) < 10 || mb_strlen($reason) > 250) {
            throw new InvalidArgumentException('Indica un motivo de entre 10 y 250 caracteres.');
        }

        $target = $this->users->find($targetId);

        if (! $target instanceof User) {
            throw new DomainException('El usuario solicitado no existe.');
        }

        $groups = $target->getGroups() ?? [];

        if (RoleAssignmentPolicy::isProtectedAccount($groups)) {
            throw new DomainException('No se pueden modificar cuentas privilegiadas desde este módulo.');
        }

        if ($operation === 'revoke' && ! in_array($group, $groups, true)) {
            throw new DomainException('El usuario no tiene asignado ese grupo.');
        }

        if ($operation === 'grant' && in_array($group, $groups, true)) {
            throw new DomainException('El usuario ya pertenece a ese grupo.');
        }

        // Los cambios y su auditoría deben confirmarse juntos.
        $this->db->transBegin();

        try {
            if ($operation === 'grant') {
                $target->addGroup($group);
            } else {
                $target->removeGroup($group);
            }

            $this->audit->record(
                (int) $actor->id,
                'access.group.' . $operation,
                'user',
                (string) $targetId,
                $reason,
                ['role' => $group, 'result' => 'success', 'source' => 'access-manager'],
            );

            if ($this->db->transStatus() === false) {
                throw new RuntimeException('No fue posible guardar el cambio de permisos.');
            }

            $this->db->transCommit();
        } catch (Throwable $exception) {
            $this->db->transRollback();

            throw $exception;
        }
    }
}
