<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AthleteModel;
use App\Models\PersonModel;
use App\Models\SportsCategoryModel;
use CodeIgniter\Shield\Entities\User;
use DomainException;
use RuntimeException;
use Throwable;

/**
 * Registro inicial por administración; no se concede automáticamente acceso
 * a un responsable por introducir su nombre/teléfono en el expediente.
 */
final class AthleteRegistrationService
{
    /**
     * @param array<string, string> $input
     */
    public function register(User $actor, array $input): int
    {
        if (! $actor->can('athletes.manage')) {
            throw new DomainException('No tienes autorización para registrar atletas.');
        }

        $first = trim($input['first_name'] ?? '');
        $last = trim($input['last_name'] ?? '');
        $birth = $input['birth_date'] ?? '';
        $joined = $input['joined_on'] ?? '';
        $guardianFirst = trim($input['guardian_first_name'] ?? '');
        $guardianLast = trim($input['guardian_last_name'] ?? '');
        $phone = trim($input['guardian_phone'] ?? '');
        $relation = $input['relationship'] ?? '';
        $categoryId = filter_var($input['sports_category_id'] ?? '', FILTER_VALIDATE_INT);

        if (! AthleteRegistrationPolicy::validName($first)
            || ! AthleteRegistrationPolicy::validName($last)
            || ! AthleteRegistrationPolicy::validName($guardianFirst)
            || ! AthleteRegistrationPolicy::validName($guardianLast)) {
            throw new DomainException('Verifica los nombres y apellidos del atleta y del adulto responsable.');
        }

        if (! AthleteRegistrationPolicy::validBirthAndEntry($birth, $joined)) {
            throw new DomainException('Verifica las fechas de nacimiento e ingreso del atleta.');
        }

        if (! AthleteRegistrationPolicy::validRelationship($relation)
            || ! AthleteRegistrationPolicy::validPhone($phone)) {
            throw new DomainException('Verifica el parentesco y teléfono del responsable.');
        }

        if ($categoryId === false || $categoryId <= 0) {
            throw new DomainException('Selecciona una categoría válida.');
        }

        $category = (new SportsCategoryModel())->find($categoryId);

        if ($category === null || ! (bool) $category['active']) {
            throw new DomainException('La categoría no está habilitada.');
        }

        $db = db_connect();
        $db->transBegin();

        try {
            $now = date('Y-m-d H:i:s');
            $personId = (new PersonModel())->insert([
                'first_name' => $guardianFirst,
                'last_name' => $guardianLast,
                'phone' => $phone === '' ? null : $phone,
                'status' => 'active',
            ], true);

            if (! is_numeric($personId)) {
                throw new RuntimeException('No fue posible registrar al responsable.');
            }

            $athleteId = (new AthleteModel())->insert([
                'first_name' => $first,
                'last_name' => $last,
                'birth_date' => $birth,
                'joined_on' => $joined,
                'sports_category_id' => $categoryId,
                'status' => 'active',
                'created_by' => (int) $actor->id,
                'created_at' => $now,
            ], true);

            if (! is_numeric($athleteId)) {
                throw new RuntimeException('No fue posible registrar al atleta.');
            }

            $linked = $db->table('athlete_guardians')->insert([
                'athlete_id' => (int) $athleteId,
                'person_id' => (int) $personId,
                'relationship' => $relation,
                'is_primary' => true,
                'verified_by' => null,
                'verified_at' => null,
                'created_at' => $now,
            ]);

            if (! $linked) {
                throw new RuntimeException('No fue posible asociar al adulto responsable.');
            }

            (new AuditLogger())->record(
                (int) $actor->id,
                'athlete.registered',
                'athlete',
                (string) $athleteId,
                'Inscripción inicial administrativa',
                ['source' => 'portal', 'result' => 'created'],
            );

            if ($db->transStatus() === false) {
                throw new RuntimeException('No fue posible completar la inscripción.');
            }

            $db->transCommit();

            return (int) $athleteId;
        } catch (Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }
    }
}
