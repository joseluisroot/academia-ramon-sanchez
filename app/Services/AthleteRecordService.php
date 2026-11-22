<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AthleteModel;
use App\Models\SportsCategoryModel;
use CodeIgniter\Shield\Entities\User;
use DomainException;
use RuntimeException;
use Throwable;

/** Cambios conservadores y auditables del expediente administrativo. */
final class AthleteRecordService
{
    public function update(User $actor, int $id, string $firstName, string $lastName, int $categoryId): void
    {
        if (! $actor->can('athletes.manage')) {
            throw new DomainException('No tienes permiso para modificar atletas.');
        }

        if ($id <= 0 || ! AthleteRegistrationPolicy::validName($firstName)
            || ! AthleteRegistrationPolicy::validName($lastName) || $categoryId <= 0) {
            throw new DomainException('Revisa los datos del atleta y la categoría.');
        }

        $model = new AthleteModel();
        $categoryModel = new SportsCategoryModel();
        $db = db_connect();
        $db->transBegin();

        try {
            $athlete = $db->table('athletes')->where('id', $id)->get()->getRowArray();

            if ($athlete === null) {
                throw new DomainException('El atleta solicitado no existe.');
            }

            $category = $categoryModel->find($categoryId);

            if ($category === null || ! (bool) $category['active']) {
                throw new DomainException('La categoría seleccionada no está habilitada.');
            }

            $changedCategory = (int) $athlete['sports_category_id'] !== $categoryId;
            $changedName = trim($firstName) !== (string) $athlete['first_name']
                || trim($lastName) !== (string) $athlete['last_name'];

            if ($changedCategory && $db->table('jersey_assignments')->where('athlete_id', $id)->countAllResults() > 0) {
                throw new DomainException('Libera primero el dorsal vigente antes de cambiar al atleta de categoría.');
            }

            if (! $changedCategory && ! $changedName) {
                throw new DomainException('No hay cambios que guardar.');
            }

            $saved = $model->update($id, [
                'first_name' => trim($firstName),
                'last_name' => trim($lastName),
                'sports_category_id' => $categoryId,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            if (! $saved) {
                throw new RuntimeException('No fue posible actualizar el expediente.');
            }

            if ($changedCategory) {
                $inserted = $db->table('athlete_category_history')->insert([
                    'athlete_id' => $id,
                    'sports_category_id' => $categoryId,
                    'effective_on' => date('Y-m-d'),
                    'reason' => 'administrative_change',
                    'recorded_by' => (int) $actor->id,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                if (! $inserted) {
                    throw new RuntimeException('No se pudo registrar el historial de categoría.');
                }
            }

            (new AuditLogger())->record(
                (int) $actor->id,
                'athlete.record.updated',
                'athlete',
                (string) $id,
                'Actualización administrativa de expediente',
                ['result' => $changedCategory ? 'category_changed' : 'name_updated', 'source' => 'portal'],
            );

            if ($db->transStatus() === false) {
                throw new RuntimeException('No fue posible completar la actualización.');
            }

            $db->transCommit();
        } catch (Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }
    }
}
