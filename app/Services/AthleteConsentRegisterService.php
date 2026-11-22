<?php

declare(strict_types=1);

namespace App\Services;

use CodeIgniter\Shield\Entities\User;
use DomainException;
use RuntimeException;
use Throwable;

/**
 * Documentation register. Administrative acceptance requires a signer, version,
 * private evidence reference and a dedicated institutional permission.
 * This NEVER grants automatic publishing rights.
 */
final class AthleteConsentRegisterService
{
    public const TYPES = ['image_use', 'data_notice'];
    public const STATES = ['pending', 'declined', 'revoked'];

    /**
     * A registrar's statement that an adult signed a specific paper version.
     * The archived signed evidence stays outside the public web root.
     * This is NOT a publishing grant or a legally conclusive signature.
     */
    public function recordAcceptance(
        User $actor,
        int $athleteId,
        int $guardianRelationId,
        string $type,
        string $documentVersion,
        string $evidenceReference,
        string $signedOn,
        string $expiresOn,
        bool $physicallyChecked,
    ): void {
        if (! $actor->can('consents.verify')) {
            throw new DomainException('Solo administración institucional puede documentar aceptaciones.');
        }

        $documentVersion = trim($documentVersion);
        $evidenceReference = trim($evidenceReference);

        if ($athleteId <= 0 || $guardianRelationId <= 0
            || ! in_array($type, self::TYPES, true)
            || ! ConsentAcceptancePolicy::validVersion($documentVersion)
            || ! ConsentAcceptancePolicy::validEvidenceReference($evidenceReference)
            || ! ConsentAcceptancePolicy::validDates($signedOn, $expiresOn)
            || ! $physicallyChecked
        ) {
            throw new DomainException('Revisa el firmante, la versión del documento, la referencia de evidencia, las fechas y la confirmación de revisión.');
        }

        $db = db_connect();
        $db->transBegin();

        try {
            // Serialise registrations for each athlete and consent type.
            $athlete = $db->query('SELECT id FROM athletes WHERE id = ? FOR UPDATE', [$athleteId])->getRowArray();
            if ($athlete === null) {
                throw new DomainException('El atleta no existe.');
            }

            $signer = $db->table('athlete_guardians ag')
                ->select('ag.id')
                ->join('persons p', 'p.id = ag.person_id')
                ->where('ag.id', $guardianRelationId)
                ->where('ag.athlete_id', $athleteId)
                ->where('p.status', 'active')
                ->get()->getRowArray();

            if ($signer === null) {
                throw new DomainException('El firmante no está registrado como responsable activo de este atleta.');
            }

            // An evidence reference identifies one signed declaration for this
            // athlete and document type; never accept the same proof twice.
            if ($db->table('athlete_consent_register')
                ->where('athlete_id', $athleteId)
                ->where('consent_type', $type)
                ->where('evidence_reference', $evidenceReference)
                ->countAllResults() > 0) {
                throw new DomainException('La evidencia de esta aceptación ya se registró anteriormente.');
            }

            $now = date('Y-m-d H:i:s');

            $inserted = $db->table('athlete_consent_register')->insert([
                'athlete_id' => $athleteId,
                'guardian_relation_id' => $guardianRelationId,
                'consent_type' => $type,
                'record_state' => 'accepted',
                'document_version' => $documentVersion,
                'evidence_reference' => $evidenceReference,
                'signed_on' => $signedOn,
                'expires_on' => $expiresOn === '' ? null : $expiresOn,
                'evidence_verified_by' => (int) $actor->id,
                'evidence_verified_at' => $now,
                'recorded_by' => (int) $actor->id,
                'recorded_at' => $now,
            ]);

            if (! $inserted) {
                throw new RuntimeException('No fue posible guardar la aceptación documentada.');
            }

            (new AuditLogger())->record(
                (int) $actor->id,
                'athlete.consent.acceptance_recorded',
                'athlete',
                (string) $athleteId,
                'Constancia administrativa de evidencia firmada',
                ['source' => 'portal', 'result' => 'accepted'],
            );

            if ($db->transStatus() === false) {
                throw new RuntimeException('No fue posible completar la aceptación documental.');
            }

            $db->transCommit();
        } catch (Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }
    }

    public function record(User $actor, int $athleteId, string $type, string $state): void
    {
        if (! $actor->can('athletes.manage')) {
            throw new DomainException('No tienes autorización para gestionar documentación.');
        }

        if ($athleteId <= 0 || ! in_array($type, self::TYPES, true)
            || ! in_array($state, self::STATES, true)) {
            throw new DomainException('Revisa el documento y el estado seleccionado.');
        }

        if ($state === 'revoked' && ! $actor->can('consents.verify')) {
            throw new DomainException('Solo administración institucional puede registrar la revocación de una aceptación.');
        }

        $db = db_connect();
        $db->transBegin();

        try {
            $athlete = $db->query('SELECT id FROM athletes WHERE id = ? FOR UPDATE', [$athleteId])->getRowArray();
            if ($athlete === null) {
                throw new DomainException('El atleta no existe.');
            }

            if ($state === 'revoked') {
                $last = $db->table('athlete_consent_register')
                    ->select('record_state')
                    ->where('athlete_id', $athleteId)
                    ->where('consent_type', $type)
                    ->orderBy('id', 'DESC')->limit(1)->get()->getRowArray();

                if ($last === null || $last['record_state'] !== 'accepted') {
                    throw new DomainException('Solo puede revocarse una aceptación documentada vigente.');
                }
            }

            // Denial/revocation are durable records. An acceptance is registered
            // separately with signed evidence and institution-only permission.
            // No register entry by itself authorizes public disclosure.
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
