<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PersonModel;
use CodeIgniter\Shield\Entities\User;
use DomainException;
use RuntimeException;
use Throwable;

/**
 * Administración de más de un adulto por atleta.
 * Añadir una relación nunca habilita acceso automáticamente.
 */
final class AthleteGuardianService
{
    public function add(User $actor, int $athleteId, int $personId, string $relationship): void
    {
        if (! $actor->can('athletes.manage')) {
            throw new DomainException('No tienes permiso para gestionar responsables.');
        }

        if ($athleteId <= 0 || $personId <= 0 || ! AthleteRegistrationPolicy::validRelationship($relationship)) {
            throw new DomainException('Selecciona un atleta, adulto y relación válidos.');
        }

        $db = db_connect();
        $db->transBegin();

        try {
            $athlete = $db->table('athletes')->where('id', $athleteId)->get()->getRowArray();
            $person = (new PersonModel())->find($personId);

            if ($athlete === null || $person === null || $person['status'] !== 'active') {
                throw new DomainException('El atleta o adulto responsable no está disponible.');
            }

            $existing = $db->table('athlete_guardians')
                ->where('athlete_id', $athleteId)
                ->where('person_id', $personId)->countAllResults();

            if ($existing > 0) {
                throw new DomainException('El adulto ya está relacionado con este atleta.');
            }

            $ok = $db->table('athlete_guardians')->insert([
                'athlete_id' => $athleteId,
                'person_id' => $personId,
                'relationship' => $relationship,
                'is_primary' => false,
                'verified_by' => null,
                'verified_at' => null,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            if (! $ok) {
                throw new RuntimeException('No se pudo registrar el responsable.');
            }

            (new AuditLogger())->record((int) $actor->id, 'family.relation.added', 'athlete', (string) $athleteId, 'Adulto relacionado pendiente de verificación', ['source' => 'portal', 'result' => 'pending']);

            if ($db->transStatus() === false) {
                throw new RuntimeException('No se pudo completar la vinculación.');
            }

            $db->transCommit();
        } catch (Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }
    }

    public function addNew(User $actor, int $athleteId, string $firstName, string $lastName, string $phone, string $relationship): void
    {
        if (! $actor->can('athletes.manage')) {
            throw new DomainException('No tienes permiso para gestionar responsables.');
        }

        $firstName = trim($firstName);
        $lastName = trim($lastName);
        $phone = trim($phone);

        if ($athleteId <= 0
            || ! AthleteRegistrationPolicy::validName($firstName)
            || ! AthleteRegistrationPolicy::validName($lastName)
            || ! AthleteRegistrationPolicy::validPhone($phone)
            || ! AthleteRegistrationPolicy::validRelationship($relationship)
        ) {
            throw new DomainException('Revisa los datos y el parentesco del nuevo adulto responsable.');
        }

        $db = db_connect();
        $db->transBegin();

        try {
            if ($db->table('athletes')->where('id', $athleteId)->countAllResults() !== 1) {
                throw new DomainException('El atleta solicitado no existe.');
            }

            $personId = (new PersonModel())->insert([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone === '' ? null : $phone,
                'status' => 'active',
            ], true);

            if (! is_numeric($personId)) {
                throw new RuntimeException('No se pudo registrar a la persona adulta.');
            }

            $linked = $db->table('athlete_guardians')->insert([
                'athlete_id' => $athleteId,
                'person_id' => (int) $personId,
                'relationship' => $relationship,
                'is_primary' => false,
                'verified_by' => null,
                'verified_at' => null,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            if (! $linked) {
                throw new RuntimeException('No se pudo relacionar al nuevo adulto.');
            }

            (new AuditLogger())->record((int) $actor->id, 'family.relation.added', 'athlete', (string) $athleteId, 'Adulto nuevo pendiente de verificación', ['source' => 'portal', 'result' => 'pending']);

            if ($db->transStatus() === false) {
                throw new RuntimeException('No fue posible completar el registro.');
            }

            $db->transCommit();
        } catch (Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }
    }

    /**
     * Elimina únicamente una asociación no verificada, no la persona adulta.
     * Impide quitar el responsable principal o dejar un atleta sin responsables.
     */
    public function removePending(User $actor, int $athleteId, int $relationshipId): void
    {
        if (! $actor->can('family.links.verify')) {
            throw new DomainException('Solo administración institucional puede quitar responsables pendientes.');
        }

        if ($athleteId <= 0 || $relationshipId <= 0) {
            throw new DomainException('Selecciona una relación familiar válida.');
        }

        $db = db_connect();
        $db->transBegin();

        try {
            // Bloquear primero el atleta para serializar modificaciones de relaciones.
            $athlete = $db->query('SELECT id FROM athletes WHERE id = ? FOR UPDATE', [$athleteId])->getRowArray();

            if ($athlete === null) {
                throw new DomainException('El atleta no existe.');
            }

            $relation = $db->table('athlete_guardians')
                ->where('id', $relationshipId)
                ->where('athlete_id', $athleteId)
                ->get()->getRowArray();

            if ($relation === null) {
                throw new DomainException('No se encontró la relación familiar.');
            }

            if ($relation['verified_at'] !== null || (bool) $relation['is_primary']) {
                throw new DomainException('Solo se puede quitar una relación pendiente que no sea principal.');
            }

            $total = $db->table('athlete_guardians')->where('athlete_id', $athleteId)->countAllResults();

            if ($total <= 1) {
                throw new DomainException('No se puede dejar al atleta sin responsable registrado.');
            }

            $db->table('athlete_guardians')
                ->where('id', $relationshipId)
                ->where('athlete_id', $athleteId)
                ->where('verified_at IS NULL', null, false)
                ->where('is_primary', 0)
                ->delete();

            if ($db->affectedRows() !== 1) {
                throw new DomainException('La relación cambió. Recarga el expediente y vuelve a revisar.');
            }

            (new AuditLogger())->record(
                (int) $actor->id,
                'family.relation.pending_removed',
                'athlete_guardian',
                (string) $relationshipId,
                'Asociación pendiente retirada administrativamente',
                ['source' => 'portal', 'result' => 'removed'],
            );

            if ($db->transStatus() === false) {
                throw new RuntimeException('No se pudo quitar la relación pendiente.');
            }

            $db->transCommit();
        } catch (Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }
    }

    public function revoke(User $actor, int $relationshipId): void
    {
        if (! $actor->can('family.links.verify')) {
            throw new DomainException('Solo administración institucional puede revocar accesos familiares.');
        }

        if ($relationshipId <= 0) {
            throw new DomainException('Selecciona una relación válida.');
        }

        $db = db_connect();
        $db->transBegin();

        try {
            $relation = $db->table('athlete_guardians')->where('id', $relationshipId)->get()->getRowArray();

            if ($relation === null || $relation['verified_at'] === null) {
                throw new DomainException('La relación no está verificada o no existe.');
            }

            $db->table('athlete_guardians')
                ->where('id', $relationshipId)
                ->where('verified_at IS NOT NULL', null, false)
                ->update(['verified_by' => null, 'verified_at' => null]);

            if ($db->affectedRows() !== 1) {
                throw new DomainException('La relación ya no está verificada.');
            }

            (new AuditLogger())->record((int) $actor->id, 'family.link.revoked', 'athlete_guardian', (string) $relationshipId, 'Revocación administrativa de acceso al expediente', ['source' => 'portal', 'result' => 'revoked']);

            if ($db->transStatus() === false) {
                throw new RuntimeException('No se pudo revocar el vínculo.');
            }

            $db->transCommit();
        } catch (Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }
    }
}
