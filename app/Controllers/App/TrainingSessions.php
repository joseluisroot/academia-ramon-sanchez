<?php

declare(strict_types=1);

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Services\TrainingSessionService;
use App\Services\CoachCategoryScopeService;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use Throwable;

final class TrainingSessions extends BaseController
{
    public function index(): string|ResponseInterface
    {
        $actor = auth()->user();
        $supervisor = CoachCategoryScopeService::isSupervisor($actor);
        $coach = CoachCategoryScopeService::isCoach($actor);

        if (! $supervisor && ! $coach) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        $db = db_connect();
        $query = $db->table('training_sessions s')
            ->select('s.id, s.training_date, s.starts_at, s.ends_at, s.location_name, s.status');

        if (! $supervisor) {
            $allowed = CoachCategoryScopeService::allowedCategoryIds($actor);
            if ($allowed === []) {
                $query->where('s.id', 0);
            } else {
                $query->join('training_session_categories sc', 'sc.session_id = s.id')
                    ->whereIn('sc.sports_category_id', $allowed)
                    ->groupBy('s.id, s.training_date, s.starts_at, s.ends_at, s.location_name, s.status');
            }
        }

        return view('app/training/index', [
            'title' => 'Entrenamientos · Academia Ramón Sánchez',
            'canSchedule' => $supervisor,
            'sessions' => $query
                ->orderBy('s.training_date', 'DESC')->orderBy('s.id', 'DESC')->limit(40)
                ->get()->getResultArray(),
            'categories' => $supervisor ? $db->table('sports_categories')
                ->where('active', 1)->orderBy('code')->get()->getResultArray() : [],
        ]);
    }

    public function create(): RedirectResponse|ResponseInterface
    {
        $actor = auth()->user();
        if (! TrainingSessionService::canManage($actor)) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        try {
            $sessionId = (new TrainingSessionService())->schedule(
                $actor,
                (string) $this->request->getPost('training_date'),
                (string) $this->request->getPost('starts_at'),
                (string) $this->request->getPost('ends_at'),
                (string) $this->request->getPost('location_name'),
                (array) $this->request->getPost('category_ids'),
            );
        } catch (DomainException $exception) {
            return redirect()->to('/app/entrenamientos')->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            log_message('error', 'No se pudo programar sesión deportiva.');
            return redirect()->to('/app/entrenamientos')->with('error', 'No fue posible programar la sesión.');
        }

        return redirect()->to('/app/entrenamientos/' . $sessionId)
            ->with('message', 'Entrenamiento programado. Sus categorías quedaron asociadas.');
    }

    public function show(int $id): string|ResponseInterface
    {
        $actor = auth()->user();
        $supervisor = CoachCategoryScopeService::isSupervisor($actor);
        if (! $supervisor && ! CoachCategoryScopeService::canViewSession($actor, $id)) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        $db = db_connect();
        $session = $db->table('training_sessions')->where('id', $id)->get()->getRowArray();

        if ($session === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $categoryQuery = $db->table('training_session_categories sc')
            ->select('sc.sports_category_id, c.code AS category_code')
            ->join('sports_categories c', 'c.id = sc.sports_category_id')
            ->where('sc.session_id', $id);

        if (! $supervisor) {
            $categoryQuery->whereIn('sc.sports_category_id', CoachCategoryScopeService::allowedCategoryIds($actor));
        }

        $categories = $categoryQuery->get()->getResultArray();

        $ids = array_map(static fn (array $row): int => (int) $row['sports_category_id'], $categories);

        if ($session['status'] === 'completed') {
            // Immutable roster at closure, not the athlete's current category/status.
            $rosterQuery = $db->table('training_session_roster tr')
                ->select('a.id, a.first_name, a.last_name, c.code AS category_code, ta.attendance_status')
                ->join('athletes a', 'a.id = tr.athlete_id')
                ->join('sports_categories c', 'c.id = tr.sports_category_id')
                ->join('training_attendance ta', 'ta.session_id = tr.session_id AND ta.athlete_id = tr.athlete_id', 'left')
                ->where('tr.session_id', $id);

            if ($ids === []) {
                $rosterQuery->where('tr.id', 0);
            } else {
                $rosterQuery->whereIn('tr.sports_category_id', $ids);
            }

            $athletes = $rosterQuery->orderBy('c.code')->orderBy('a.last_name')->orderBy('a.first_name')
                ->get()->getResultArray();

            // Legacy: sessions closed before roster migration have no snapshot.
            // Recover only actual attendance rows; never invent athletes/absences.
            // If the historical category is NULL, include that mark only when
            // the session had exactly one category. Otherwise leave it for
            // administrator review rather than assigning an unproven category.
            $rosterCount = $db->table('training_session_roster')
                ->where('session_id', $id)->countAllResults();

            if ($rosterCount === 0) {
                $historicalQuery = $db->table('training_attendance ta')
                    ->select('a.id, a.first_name, a.last_name, c.code AS category_code, ta.attendance_status')
                    ->join('athletes a', 'a.id = ta.athlete_id')
                    ->join('sports_categories c', 'c.id = ta.sports_category_id')
                    ->where('ta.session_id', $id);

                if ($ids === []) {
                    $historicalQuery->where('ta.id', 0);
                } else {
                    $historicalQuery->whereIn('ta.sports_category_id', $ids);
                }

                $athletes = $historicalQuery->orderBy('c.code')->orderBy('a.last_name')
                    ->orderBy('a.first_name')->get()->getResultArray();

                if ($supervisor && count($categories) === 1) {
                    // An unclassified legacy mark can be displayed in the only
                    // session category, but it remains NULL in persistent data.
                    $legacy = $db->table('training_attendance ta')
                        ->select('a.id, a.first_name, a.last_name, ta.attendance_status')
                        ->join('athletes a', 'a.id = ta.athlete_id')
                        ->where('ta.session_id', $id)
                        ->where('ta.sports_category_id IS NULL', null, false)
                        ->get()->getResultArray();
                    foreach ($legacy as $entry) {
                        $entry['category_code'] = $categories[0]['category_code'] . ' (sin verificar)';
                        $athletes[] = $entry;
                    }
                }
            }
        } elseif ($session['status'] === 'cancelled') {
            $athletes = [];
        } else {
            $athletes = $ids === [] ? [] : $db->table('athletes a')
                ->select('a.id, a.first_name, a.last_name, c.code AS category_code, ta.attendance_status')
                ->join('sports_categories c', 'c.id = a.sports_category_id')
                ->join('training_attendance ta', 'ta.athlete_id = a.id AND ta.session_id = ' . (int) $id, 'left')
                ->where('a.status', 'active')
                ->whereIn('a.sports_category_id', $ids)
                ->orderBy('c.code')->orderBy('a.last_name')->orderBy('a.first_name')
                ->get()->getResultArray();
        }

        return view('app/training/show', [
            'title' => 'Asistencia · Academia Ramón Sánchez',
            'session' => $session,
            'categories' => $categories,
            'athletes' => $athletes,
            'canRecord' => $session['status'] === 'scheduled' && (string) $session['training_date'] <= date('Y-m-d'),
            'canClose' => $supervisor && $session['status'] === 'scheduled',
            'historicalRoster' => $session['status'] === 'completed',
            'legacyRoster' => $session['status'] === 'completed' && $rosterCount === 0,
            'statusHistory' => $db->table('training_session_status_history')
                ->select('from_status, to_status, changed_at')
                ->where('session_id', $id)->orderBy('id', 'DESC')->get()->getResultArray(),
            'attendanceTotals' => $this->getScopedAttendanceTotals($db, $id, $supervisor, $ids),
        ]);
    }

    /** Total counts must never reveal athletes outside coach categories. */
    private function getScopedAttendanceTotals(
        \CodeIgniter\Database\BaseConnection $db,
        int $sessionId,
        bool $supervisor,
        array $categoryIds,
    ): array {
        $query = $db->table('training_attendance')
            ->select('attendance_status, COUNT(id) AS quantity')
            ->where('session_id', $sessionId);

        if (! $supervisor) {
            if ($categoryIds === []) {
                return [];
            }
            $query->whereIn('sports_category_id', $categoryIds);
        }

        return $query->groupBy('attendance_status')->get()->getResultArray();
    }

    public function changeStatus(int $sessionId): RedirectResponse|ResponseInterface
    {
        $actor = auth()->user();
        if (! CoachCategoryScopeService::isSupervisor($actor)) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        try {
            (new TrainingSessionService())->changeStatus(
                $actor, $sessionId, (string) $this->request->getPost('status'),
            );
        } catch (DomainException $exception) {
            return redirect()->to('/app/entrenamientos/' . $sessionId)->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            log_message('error', 'No se pudo cambiar el estado del entrenamiento.');
            return redirect()->to('/app/entrenamientos/' . $sessionId)->with('error', 'No fue posible completar el cambio.');
        }

        return redirect()->to('/app/entrenamientos/' . $sessionId)->with('message', 'Estado de la sesión guardado con historial.');
    }

    public function report(): string|ResponseInterface
    {
        $actor = auth()->user();
        $supervisor = CoachCategoryScopeService::isSupervisor($actor);
        if (! $supervisor && ! CoachCategoryScopeService::isCoach($actor)) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        $db = db_connect();
        $query = $db->table('training_attendance ta')
            ->select('ta.athlete_id, a.first_name, a.last_name, c.code AS category_code, COUNT(ta.id) AS marked, '
                . "SUM(CASE WHEN ta.attendance_status = 'present' THEN 1 ELSE 0 END) AS present_count, "
                . "SUM(CASE WHEN ta.attendance_status = 'absent' THEN 1 ELSE 0 END) AS absent_count, "
                . "SUM(CASE WHEN ta.attendance_status = 'excused' THEN 1 ELSE 0 END) AS excused_count", false)
            ->join('training_sessions s', 's.id = ta.session_id')
            ->join('athletes a', 'a.id = ta.athlete_id')
            ->join('sports_categories c', 'c.id = ta.sports_category_id')
            ->where('s.status', 'completed')
            ->where('ta.sports_category_id IS NOT NULL', null, false);

        if (! $supervisor) {
            $allowed = CoachCategoryScopeService::allowedCategoryIds($actor);
            if ($allowed === []) {
                $query->where('ta.id', 0);
            } else {
                $query->whereIn('ta.sports_category_id', $allowed);
            }
        }

        $results = $query->groupBy('ta.athlete_id, a.first_name, a.last_name, c.code')
            ->orderBy('c.code', 'ASC')->orderBy('a.last_name', 'ASC')->orderBy('a.first_name', 'ASC')
            ->get()->getResultArray();

        return view('app/training/report', [
            'title' => 'Reporte de asistencia · Academia Ramón Sánchez',
            'results' => $results,
            'supervisor' => $supervisor,
        ]);
    }

    public function attendance(int $sessionId): RedirectResponse|ResponseInterface
    {
        $actor = auth()->user();
        if (! CoachCategoryScopeService::isSupervisor($actor) && ! CoachCategoryScopeService::canViewSession($actor, $sessionId)) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        $athleteId = filter_var($this->request->getPost('athlete_id'), FILTER_VALIDATE_INT);

        try {
            (new TrainingSessionService())->recordAttendance(
                $actor,
                $sessionId,
                $athleteId === false ? 0 : $athleteId,
                (string) $this->request->getPost('status'),
            );
        } catch (DomainException $exception) {
            return redirect()->to('/app/entrenamientos/' . $sessionId)->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            log_message('error', 'No se pudo registrar asistencia deportiva.');
            return redirect()->to('/app/entrenamientos/' . $sessionId)->with('error', 'No fue posible registrar la asistencia.');
        }

        return redirect()->to('/app/entrenamientos/' . $sessionId)
            ->with('message', 'Asistencia registrada correctamente.');
    }
}
