<?php

declare(strict_types=1);

namespace App\Services;

use CodeIgniter\Shield\Entities\User;
use DomainException;
use RuntimeException;
use Throwable;

/**
 * Dorsal vigente: único entre atletas de la misma categoría, sin
 * reasignación automática al cambiar de categoría.
 */
final class JerseyAssignmentService
{
    public function assign(User $actor, int $athleteId, int $number): void
    {
        if (! $actor->can('athletes.manage')) {
            throw new DomainException('No tienes autorización para asignar dorsales.');
        }

        if ($athleteId <= 0 || $number < 1 || $number > 99) {
            throw new DomainException('Selecciona un atleta y un dorsal del 1 al 99.');
        }

        $db = db_connect();
        $db->transBegin();

        try {
            $athlete = $db->table('athletes')->where('id', $athleteId)->get()->getRowArray();

            if ($athlete === null || $athlete['status'] !== 'active') {
                throw new DomainException('El atleta no se encuentra activo.');
            }

            $categoryId = (int) $athlete['sports_category_id'];

            // Serializa las asignaciones por categoría en MySQL/MariaDB.
            // El índice único (categoría,número) protege contra otros escritores.
            $category = $db->query('SELECT id FROM sports_categories WHERE id = ? FOR UPDATE', [$categoryId])->getRowArray();

            if ($category === null) {
                throw new DomainException('La categoría no existe.');
            }

            $old = $db->table('jersey_assignments')->where('athlete_id', $athleteId)->get()->getRowArray();

            if ($old !== null && (int) $old['jersey_number'] === $number
                && (int) $old['sports_category_id'] === $categoryId) {
                throw new DomainException('El atleta ya tiene asignado ese dorsal.');
            }

            $occupied = $db->table('jersey_assignments')
                ->where('sports_category_id', $categoryId)
                ->where('jersey_number', $number)
                ->get()->getRowArray();

            if ($occupied !== null) {
                throw new DomainException('Ese dorsal ya está asignado a otro atleta de la categoría.');
            }

            $now = date('Y-m-d H:i:s');

            if ($old !== null) {
                $db->table('jersey_assignments')->where('id', (int) $old['id'])->delete();
                $this->history($db, $athleteId, (int) $old['sports_category_id'], (int) $old['jersey_number'], 'released', $actor, $now);
            }

            if (! $db->table('jersey_assignments')->insert([
                'athlete_id' => $athleteId,
                'sports_category_id' => $categoryId,
                'jersey_number' => $number,
                'assigned_by' => (int) $actor->id,
                'assigned_at' => $now,
            ])) {
                throw new RuntimeException('No se pudo asignar el dorsal.');
            }

            $this->history($db, $athleteId, $categoryId, $number, 'assigned', $actor, $now);

            (new AuditLogger())->record((int) $actor->id, 'athlete.jersey.assigned', 'athlete', (string) $athleteId, 'Asignación administrativa de dorsal', ['source' => 'portal', 'result' => 'assigned']);

            if ($db->transStatus() === false) {
                throw new RuntimeException('No se pudo completar la asignación.');
            }

            $db->transCommit();
        } catch (Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }
    }

    public function release(User $actor, int $athleteId): void
    {
        if (! $actor->can('athletes.manage')) {
            throw new DomainException('No tienes autorización para liberar dorsales.');
        }

        $db = db_connect();
        $db->transBegin();

        try {
            // La categoría del atleta es el candado de todas las acciones
            // de dorsales; tomarlo antes de consultar el dorsal activo.
            $athlete = $db->table('athletes')->where('id', $athleteId)->get()->getRowArray();

            if ($athlete === null) {
                throw new DomainException('El atleta no existe.');
            }

            $categoryId = (int) $athlete['sports_category_id'];
            $db->query('SELECT id FROM sports_categories WHERE id = ? FOR UPDATE', [$categoryId]);

            $old = $db->table('jersey_assignments')->where('athlete_id', $athleteId)->get()->getRowArray();

            if ($old === null) {
                throw new DomainException('El atleta no tiene dorsal asignado.');
            }

            if ((int) $old['sports_category_id'] !== $categoryId) {
                throw new DomainException('El dorsal está vinculado a otra categoría. Solicita revisión administrativa.');
            }

            $db->table('jersey_assignments')->where('id', (int) $old['id'])->delete();

            $this->history($db, $athleteId, $categoryId, (int) $old['jersey_number'], 'released', $actor, date('Y-m-d H:i:s'));
            (new AuditLogger())->record((int) $actor->id, 'athlete.jersey.released', 'athlete', (string) $athleteId, 'Liberación administrativa de dorsal', ['source' => 'portal', 'result' => 'released']);

            if ($db->transStatus() === false) {
                throw new RuntimeException('No se pudo liberar el dorsal.');
            }

            $db->transCommit();
        } catch (Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }
    }

    private function history($db, int $athleteId, int $categoryId, int $number, string $event, User $actor, string $at): void
    {
        if (! $db->table('jersey_assignment_history')->insert([
            'athlete_id' => $athleteId,
            'sports_category_id' => $categoryId,
            'jersey_number' => $number,
            'event' => $event,
            'actor_user_id' => (int) $actor->id,
            'occurred_at' => $at,
        ])) {
            throw new RuntimeException('No se pudo registrar el historial del dorsal.');
        }
    }
}
