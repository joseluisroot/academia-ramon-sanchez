<?php

declare(strict_types=1);

namespace App\Services;

use CodeIgniter\Shield\Entities\User;
use DomainException;
use RuntimeException;
use Throwable;

final class AthleteLifecycleService
{
    public const STATES = ['active', 'inactive', 'withdrawn'];

    /**
     * Reason codes intentionally contain no free-text sensitive details.
     * Re-entry after withdrawal requires a separate approved procedure.
     */
    public const REASONS = ['temporary_pause', 'administrative', 'family_request', 'returned'];

    public function change(User $actor, int $athleteId, string $status, string $reason): void
    {
        if (! $actor->can('athletes.manage')) {
            throw new DomainException('No tienes permiso para modificar el estado del atleta.');
        }

        if ($athleteId <= 0 || ! in_array($status, self::STATES, true)
            || ! in_array($reason, self::REASONS, true)) {
            throw new DomainException('Selecciona un estado y un motivo válidos.');
        }

        $db = db_connect();
        $db->transBegin();

        try {
            $athlete = $db->query('SELECT id, status FROM athletes WHERE id = ? FOR UPDATE', [$athleteId])->getRowArray();
            if ($athlete === null) {
                throw new DomainException('El atleta no existe.');
            }

            $from = (string) $athlete['status'];
            $allowed = [
                'active' => ['inactive', 'withdrawn'],
                'inactive' => ['active', 'withdrawn'],
                'withdrawn' => [],
            ];

            if (! in_array($status, $allowed[$from] ?? [], true)) {
                throw new DomainException('La transición solicitada no está permitida.');
            }

            $db->table('athletes')->where('id', $athleteId)->where('status', $from)
                ->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);

            if ($db->affectedRows() !== 1) {
                throw new RuntimeException('El estado del atleta cambió durante esta operación.');
            }

            if (! $db->table('athlete_status_history')->insert([
                'athlete_id' => $athleteId,
                'from_status' => $from,
                'to_status' => $status,
                'reason_code' => $reason,
                'changed_by' => (int) $actor->id,
                'changed_at' => date('Y-m-d H:i:s'),
            ])) {
                throw new RuntimeException('No se pudo registrar el historial de estado.');
            }

            (new AuditLogger())->record((int) $actor->id, 'athlete.status.changed', 'athlete',
                (string) $athleteId, 'Transición de estado administrativa',
                ['source' => 'portal', 'result' => $status]);

            if ($db->transStatus() === false) {
                throw new RuntimeException('No se pudo finalizar el cambio de estado.');
            }

            $db->transCommit();
        } catch (Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }
    }
}
