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

        $athletes = $ids === [] ? [] : $db->table('athletes a')
            ->select('a.id, a.first_name, a.last_name, c.code AS category_code, ta.attendance_status')
            ->join('sports_categories c', 'c.id = a.sports_category_id')
            ->join('training_attendance ta', 'ta.athlete_id = a.id AND ta.session_id = ' . (int) $id, 'left')
            ->where('a.status', 'active')
            ->whereIn('a.sports_category_id', $ids)
            ->orderBy('c.code')->orderBy('a.last_name')->orderBy('a.first_name')
            ->get()->getResultArray();

        return view('app/training/show', [
            'title' => 'Asistencia · Academia Ramón Sánchez',
            'session' => $session,
            'categories' => $categories,
            'athletes' => $athletes,
            'canRecord' => $session['status'] === 'scheduled' && (string) $session['training_date'] <= date('Y-m-d'),
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
