<?php

declare(strict_types=1);

namespace App\Services;

use CodeIgniter\Shield\Entities\User;
use DomainException;
use RuntimeException;
use Throwable;

/**
 * Documentation state tracker only. NOT proof of valid consent or permission
 * to use photographs / publish individual athlete information.
 */
final class AthleteConsentRegisterService
{
    public const TYPES = ['image_use', 'data_notice'];
    public const STATES = ['pending', 'declined', 'revoked'];

    public function record(User $actor, int $athleteId, string $type, string $state): void
    {
        if (! $actor->can('athletes.manage')) {
            throw new DomainException('No tienes autorización para gestionar documentación.');
        }

        if ($athleteId <= 0 || ! in_array($type, self::TYPES, true)
            || ! in_array($state, self::STATES, true)) {
            throw new DomainException('Revisa el documento y el estado seleccionado.');
        }

        $db = db_connect();
        $db->transBegin();

        try {
            if ($db->table('athletes')->where('id', $athleteId)->countAllResults() !== 1) {
                throw new DomainException('El atleta no existe.');
            }

            // Denial and revocation must be durable and immediately respected by
            // future publishing logic. No approval state is available in this cut.
            if (! $db->table('athlete_consent_register')->insert([
                'athlete_id' => $athleteId,
                'guardian_relation_id' => null,
                'consent_type' => $type,
                'record_state' => $state,
                'document_version' => null,
                'recorded_by' => (int) $actor->id,
                'recorded_at' => date('Y-m-d H:i:s'),
            ])) {
                throw new RuntimeException('No se pudo registrar el estado documental.');
            }

            (new AuditLogger())->record((int) $actor->id, 'athlete.consent_recorded', 'athlete',
                (string) $athleteId, 'Estado documental registrado',
                ['source' => 'portal', 'result' => $state]);

            if ($db->transStatus() === false) {
                throw new RuntimeException('No se pudo completar el registro documental.');
            }

            $db->transCommit();
        } catch (Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }
    }
}
