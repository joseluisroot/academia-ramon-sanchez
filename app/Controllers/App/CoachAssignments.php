<?php

declare(strict_types=1);

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Services\CoachCategoryScopeService;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Shield\Models\UserModel;
use DomainException;
use Throwable;

final class CoachAssignments extends BaseController
{
    public function index(): string|ResponseInterface
    {
        $actor = auth()->user();
        if ($actor === null || ! $actor->can('training.assign') || ! $actor->inGroup('institutional_admin')) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        /** @var UserModel $users */
        $users = model(UserModel::class);
        $coaches = [];
        foreach ($users->orderBy('id', 'ASC')->findAll() as $user) {
            if ($user->inGroup('coach') && $user->active) {
                $coaches[] = ['id' => (int) $user->id, 'email' => (string) $user->email];
            }
        }

        $db = db_connect();
        return view('app/training/assignments', [
            'title' => 'Asignaciones de entrenadores · Academia Ramón Sánchez',
            'coaches' => $coaches,
            'categories' => $db->table('sports_categories')->where('active', 1)->orderBy('code')->get()->getResultArray(),
            'assignments' => $db->table('coach_category_assignments a')
                ->select('a.id, a.user_id, a.sports_category_id, a.active, a.assigned_at, a.revoked_at, c.code AS category_code')
                ->join('sports_categories c', 'c.id = a.sports_category_id')
                ->orderBy('a.user_id')->orderBy('c.code')->get()->getResultArray(),
        ]);
    }

    public function change(): RedirectResponse|ResponseInterface
    {
        $actor = auth()->user();
        if ($actor === null || ! $actor->can('training.assign') || ! $actor->inGroup('institutional_admin')) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        $userId = filter_var($this->request->getPost('user_id'), FILTER_VALIDATE_INT);
        $categoryId = filter_var($this->request->getPost('sports_category_id'), FILTER_VALIDATE_INT);
        $operation = (string) $this->request->getPost('operation');

        try {
            (new CoachCategoryScopeService())->change(
                $actor, $userId === false ? 0 : $userId,
                $categoryId === false ? 0 : $categoryId, $operation,
            );
        } catch (DomainException $exception) {
            return redirect()->to('/app/entrenamientos/entrenadores')->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            log_message('error', 'No se pudo modificar el alcance de entrenamiento del entrenador.');
            return redirect()->to('/app/entrenamientos/entrenadores')
                ->with('error', 'No fue posible modificar la asignación.');
        }

        return redirect()->to('/app/entrenamientos/entrenadores')
            ->with('message', $operation === 'assign' ? 'Categoría asignada al entrenador.' : 'Acceso a la categoría revocado.');
    }
}
