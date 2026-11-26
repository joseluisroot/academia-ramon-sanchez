<?php

declare(strict_types=1);

namespace App\Services;

use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use DomainException;
use RuntimeException;
use Throwable;

/**
 * La asignación de rol Coach NO concede acceso a todos los menores.
 * El permiso y la relación por categoría deben validarse en cada consulta.
 */
final class CoachCategoryScopeService
{
    public static function isSupervisor(?User $actor): bool
    {
        return $actor !== null && $actor->can('training.manage')
            && ($actor->inGroup('institutional_admin') || $actor->inGroup('sports_director'));
    }

    public static function isCoach(?User $actor): bool
    {
        return $actor !== null && $actor->can('training.manage') && $actor->inGroup('coach');
    }

    /** @return int[] */
    public static function allowedCategoryIds(?User $actor): array
    {
        if (self::isSupervisor($actor)) {
            return array_map('intval', array_column(
                db_connect()->table('sports_categories')->select('id')
                    ->where('active', 1)->get()->getResultArray(),
                'id',
            ));
        }

        if (! self::isCoach($actor)) {
            return [];
        }

        return array_map('intval', array_column(
            db_connect()->table('coach_category_assignments cca')
                ->select('cca.sports_category_id')
                ->join('sports_categories sc', 'sc.id = cca.sports_category_id')
                ->where('cca.user_id', (int) $actor->id)->where('cca.active', 1)
                ->where('sc.active', 1)->get()->getResultArray(),
            'sports_category_id',
        ));
    }

    /** Supervisores gestionan todo; entrenadores solo sesiones con categorías asignadas. */
    public static function canViewSession(?User $actor, int $sessionId): bool
    {
        if ($sessionId <= 0) {
            return false;
        }

        if (self::isSupervisor($actor)) {
            return true;
        }

        if (! self::isCoach($actor)) {
            return false;
        }

        $ids = self::allowedCategoryIds($actor);

        return $ids !== [] && db_connect()->table('training_session_categories')
            ->where('session_id', $sessionId)->whereIn('sports_category_id', $ids)
            ->countAllResults() > 0;
    }

    public function change(User $actor, int $userId, int $categoryId, string $operation): void
    {
        // Exclusively institutional administrators may grant/revoke scopes.
        if (! $actor->can('training.assign') || ! $actor->inGroup('institutional_admin')) {
            throw new DomainException('No tienes permiso para asignar categorías a entrenadores.');
        }

        if ($userId <= 0 || $categoryId <= 0 || ! in_array($operation, ['assign', 'revoke'], true)) {
            throw new DomainException('Selecciona entrenador, categoría y operación válidos.');
        }

        $db = db_connect();
        $db->transBegin();
        try {
            /** @var UserModel $userModel */
            $userModel = model(UserModel::class);
            $coach = $userModel->find($userId);

            if ($coach === null || ! $coach->inGroup('coach') || ! $coach->active) {
                throw new DomainException('El usuario debe tener una cuenta activa con rol Entrenador.');
            }

            $category = $db->table('sports_categories')->where('id', $categoryId)
                ->where('active', 1)->get()->getRowArray();

            if ($category === null) {
                throw new DomainException('La categoría está inactiva o no existe.');
            }

            $existing = $db->table('coach_category_assignments')
                ->where('user_id', $userId)
                ->where('sports_category_id', $categoryId)
                ->get()->getRowArray();
            $now = date('Y-m-d H:i:s');

            if ($operation === 'assign') {
                if ($existing !== null && (bool) $existing['active']) {
                    throw new DomainException('El entrenador ya tiene esta categoría asignada.');
                }
                if ($existing === null) {
                    $ok = $db->table('coach_category_assignments')->insert([
                        'user_id' => $userId,
                        'sports_category_id' => $categoryId,
                        'active' => 1,
                        'assigned_by' => (int) $actor->id,
                        'assigned_at' => $now,
                    ]);
                } else {
                    $ok = $db->table('coach_category_assignments')
                        ->where('id', (int) $existing['id'])->where('active', 0)
                        ->update([
                            'active' => 1,
                            'assigned_by' => (int) $actor->id,
                            'assigned_at' => $now,
                            'revoked_by' => null,
                            'revoked_at' => null,
                        ]);
                }
            } else {
                if ($existing === null || ! (bool) $existing['active']) {
                    throw new DomainException('La categoría no está asignada a este entrenador.');
                }

                $ok = $db->table('coach_category_assignments')
                    ->where('id', (int) $existing['id'])->where('active', 1)
                    ->update(['active' => 0, 'revoked_by' => (int) $actor->id, 'revoked_at' => $now]);
            }

            if (! $ok || $db->affectedRows() !== 1) {
                throw new RuntimeException('No se pudo modificar la asignación del entrenador.');
            }

            (new AuditLogger())->record(
                (int) $actor->id,
                $operation === 'assign' ? 'training.coach_scope.assigned' : 'training.coach_scope.revoked',
                'coach_category_assignment',
                (string) ($existing['id'] ?? $db->insertID()),
                'Cambio de categoría autorizada para entrenador',
                ['source' => 'portal', 'result' => $operation],
            );

            if ($db->transStatus() === false) {
                throw new RuntimeException('La operación de asignación no pudo completarse.');
            }

            $db->transCommit();
        } catch (Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }
    }
}
