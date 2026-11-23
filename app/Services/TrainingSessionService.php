<?php

declare(strict_types=1);

namespace App\Services;

use CodeIgniter\Shield\Entities\User;
use DomainException;
use RuntimeException;
use Throwable;

/**
 * Primer corte: administración o dirección deportiva. La asignación de
 * entrenadores a categorías se implementará antes de habilitarles el módulo.
 */
final class TrainingSessionService
{
    public static function canManage(?User $actor): bool
    {
        return CoachCategoryScopeService::isSupervisor($actor);
    }

    public function schedule(User $actor, string $date, string $starts, string $ends, string $location, array $categoryIds): int
    {
        if (! self::canManage($actor)) {
            throw new DomainException('Solo administración o dirección deportiva pueden programar sesiones en este corte.');
        }

        $day = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        if ($day === false || $day->format('Y-m-d') !== $date || $date < date('Y-m-d')) {
            throw new DomainException('Selecciona una fecha actual o futura válida.');
        }

        $start = \DateTimeImmutable::createFromFormat('!H:i', $starts);
        $end = \DateTimeImmutable::createFromFormat('!H:i', $ends);
        if ($start === false || $end === false || $start->format('H:i') !== $starts || $end->format('H:i') !== $ends || $starts >= $ends) {
            throw new DomainException('Introduce un horario válido; la salida debe ser posterior al inicio.');
        }

        $location = trim($location);
        if (mb_strlen($location) < 3 || mb_strlen($location) > 120) {
            throw new DomainException('La ubicación debe tener entre 3 y 120 caracteres.');
        }

        $ids = [];
        foreach ($categoryIds as $rawId) {
            $id = filter_var($rawId, FILTER_VALIDATE_INT);
            if ($id === false || $id <= 0) {
                throw new DomainException('La categoría seleccionada no es válida.');
            }
            $ids[$id] = $id;
        }

        if ($ids === []) {
            throw new DomainException('Selecciona al menos una categoría.');
        }

        $db = db_connect();
        $db->transBegin();

        try {
            $active = $db->table('sports_categories')
                ->whereIn('id', array_values($ids))->where('active', 1)->countAllResults();

            if ($active !== count($ids)) {
                throw new DomainException('Una categoría no existe o está inactiva.');
            }

            $now = date('Y-m-d H:i:s');
            if (! $db->table('training_sessions')->insert([
                'training_date' => $date,
                'starts_at' => $starts . ':00',
                'ends_at' => $ends . ':00',
                'location_name' => $location,
                'status' => 'scheduled',
                'created_by' => (int) $actor->id,
                'created_at' => $now,
            ])) {
                throw new RuntimeException('No se pudo crear la sesión.');
            }

            $sessionId = (int) $db->insertID();
            foreach ($ids as $categoryId) {
                if (! $db->table('training_session_categories')->insert([
                    'session_id' => $sessionId,
                    'sports_category_id' => $categoryId,
                ])) {
                    throw new RuntimeException('No se pudo asociar la categoría.');
                }
            }

            (new AuditLogger())->record((int) $actor->id, 'training.session.created', 'training_session',
                (string) $sessionId, 'Sesión deportiva creada', ['source' => 'portal', 'result' => 'scheduled']);

            if ($db->transStatus() === false) {
                throw new RuntimeException('No se pudo completar la programación.');
            }

            $db->transCommit();
            return $sessionId;
        } catch (Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }
    }

    public function changeStatus(User $actor, int $sessionId, string $newStatus): void
    {
        if (! CoachCategoryScopeService::isSupervisor($actor)) {
            throw new DomainException('Solo administración y dirección deportiva pueden cerrar o cancelar sesiones.');
        }
        if ($sessionId <= 0 || ! in_array($newStatus, ['completed', 'cancelled'], true)) {
            throw new DomainException('Selecciona una sesión y un estado válidos.');
        }

        $db = db_connect();
        $db->transBegin();
        try {
            $session = $db->query('SELECT id, status, training_date FROM training_sessions WHERE id = ? FOR UPDATE', [$sessionId])->getRowArray();
            if ($session === null || $session['status'] !== 'scheduled') {
                throw new DomainException('Solo pueden finalizarse o cancelarse sesiones programadas.');
            }

            $marks = $db->table('training_attendance')->where('session_id', $sessionId)->countAllResults();

            if ($newStatus === 'completed' && ((string) $session['training_date'] > date('Y-m-d') || $marks === 0)) {
                throw new DomainException('Para finalizar, la sesión debe haber llegado a su fecha y tener al menos una asistencia registrada.');
            }
            if ($newStatus === 'cancelled' && $marks > 0) {
                throw new DomainException('No se puede cancelar una sesión con asistencias registradas.');
            }

            if ($newStatus === 'completed') {
                // Freeze the roster as observed at closure. Include previously marked
                // athletes even if their current category/status has since changed.
                $current = $db->table('athletes a')
                    ->select('a.id AS athlete_id, a.sports_category_id')
                    ->join('training_session_categories tc', 'tc.sports_category_id = a.sports_category_id')
                    ->where('tc.session_id', $sessionId)->where('a.status', 'active')
                    ->get()->getResultArray();

                $marked = $db->table('training_attendance ta')
                    ->select('ta.athlete_id, ta.sports_category_id')
                    ->where('ta.session_id', $sessionId)
                    ->where('ta.sports_category_id IS NOT NULL', null, false)
                    ->get()->getResultArray();

                $roster = [];
                foreach ($current as $entry) {
                    $roster[(int) $entry['athlete_id']] = (int) $entry['sports_category_id'];
                }
                foreach ($marked as $entry) {
                    // Preserve the category explicitly recorded at attendance time.
                    $roster[(int) $entry['athlete_id']] = (int) $entry['sports_category_id'];
                }

                foreach ($roster as $athleteId => $categoryId) {
                    if (! $db->table('training_session_roster')->insert([
                        'session_id' => $sessionId,
                        'athlete_id' => $athleteId,
                        'sports_category_id' => $categoryId,
                        'captured_at' => date('Y-m-d H:i:s'),
                    ])) {
                        throw new RuntimeException('No fue posible congelar la plantilla histórica.');
                    }
                }
            }

            $db->table('training_sessions')->where('id', $sessionId)->where('status', 'scheduled')
                ->update(['status' => $newStatus, 'updated_at' => date('Y-m-d H:i:s')]);
            if ($db->affectedRows() !== 1) {
                throw new RuntimeException('La sesión cambió durante esta operación.');
            }

            if (! $db->table('training_session_status_history')->insert([
                'session_id' => $sessionId,
                'from_status' => 'scheduled',
                'to_status' => $newStatus,
                'changed_by' => (int) $actor->id,
                'changed_at' => date('Y-m-d H:i:s'),
            ])) {
                throw new RuntimeException('No se pudo guardar el historial de la sesión.');
            }

            (new AuditLogger())->record((int) $actor->id, 'training.session.status_changed',
                'training_session', (string) $sessionId, 'Cambio administrativo de estado',
                ['source' => 'portal', 'result' => $newStatus]);
            if ($db->transStatus() === false) {
                throw new RuntimeException('No pudo completarse la operación.');
            }
            $db->transCommit();
        } catch (Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }
    }

    public function recordAttendance(User $actor, int $sessionId, int $athleteId, string $status): void
    {
        if (! self::canManage($actor) && ! CoachCategoryScopeService::isCoach($actor)) {
            throw new DomainException('No tienes autorización para controlar asistencia.');
        }

        if ($sessionId <= 0 || $athleteId <= 0 || ! in_array($status, ['present', 'absent', 'excused'], true)) {
            throw new DomainException('Revisa el atleta y el estado de asistencia.');
        }

        $db = db_connect();
        $db->transBegin();
        try {
            $session = $db->query('SELECT id, status, training_date FROM training_sessions WHERE id = ? FOR UPDATE', [$sessionId])->getRowArray();
            if ($session === null || $session['status'] !== 'scheduled') {
                throw new DomainException('La sesión no existe o no admite cambios de asistencia.');
            }

            if ((string) $session['training_date'] > date('Y-m-d')) {
                throw new DomainException('No se permite registrar asistencia antes de la fecha de la sesión.');
            }

            $athlete = $db->table('athletes')->where('id', $athleteId)
                ->where('status', 'active')->get()->getRowArray();

            if ($athlete === null) {
                throw new DomainException('El atleta no está activo.');
            }

            $inCategory = $db->table('training_session_categories')
                ->where('session_id', $sessionId)
                ->where('sports_category_id', (int) $athlete['sports_category_id'])->countAllResults();

            if ($inCategory !== 1) {
                throw new DomainException('La categoría del atleta no participa en esta sesión.');
            }

            if (! CoachCategoryScopeService::isSupervisor($actor)
                && ! in_array((int) $athlete['sports_category_id'], CoachCategoryScopeService::allowedCategoryIds($actor), true)) {
                throw new DomainException('No puedes registrar asistencia de una categoría no asignada.');
            }

            $existing = $db->table('training_attendance')->where('session_id', $sessionId)
                ->where('athlete_id', $athleteId)->get()->getRowArray();
            $now = date('Y-m-d H:i:s');

            if ($existing === null) {
                $ok = $db->table('training_attendance')->insert([
                    'session_id' => $sessionId,
                    'athlete_id' => $athleteId,
                    'sports_category_id' => (int) $athlete['sports_category_id'],
                    'attendance_status' => $status,
                    'recorded_by' => (int) $actor->id,
                    'recorded_at' => $now,
                ]);
            } else {
                if ($existing['attendance_status'] === $status) {
                    throw new DomainException('La asistencia ya tiene ese estado.');
                }
                $ok = $db->table('training_attendance')->where('id', (int) $existing['id'])
                    ->update(['attendance_status' => $status, 'sports_category_id' => $existing['sports_category_id'] ?? (int) $athlete['sports_category_id'], 'recorded_by' => (int) $actor->id, 'updated_at' => $now]);
            }

            if (! $ok) {
                throw new RuntimeException('No se pudo guardar la asistencia.');
            }

            (new AuditLogger())->record((int) $actor->id, 'training.attendance.recorded',
                'training_attendance', (string) $sessionId, 'Asistencia actualizada',
                ['source' => 'portal', 'result' => $status]);

            if ($db->transStatus() === false) {
                throw new RuntimeException('No se pudo completar la asistencia.');
            }
            $db->transCommit();
        } catch (Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }
    }
}
