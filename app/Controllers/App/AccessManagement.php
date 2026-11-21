<?php

declare(strict_types=1);

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Services\RoleAssignmentService;
use App\Services\RoleAssignmentPolicy;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Shield\Models\UserModel;
use DomainException;
use InvalidArgumentException;

class AccessManagement extends BaseController
{
    public function index(): string|ResponseInterface
    {
        $actor = auth()->user();

        if ($actor === null || ! $actor->can('users.assign')) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        /** @var UserModel $users */
        $users = model(UserModel::class);

        return view('app/access/index', [
            'title' => 'Administrar accesos · Academia Ramón Sánchez',
            'users' => $users->orderBy('id', 'DESC')->paginate(20),
            'pager' => $users->pager,
            'assignableGroups' => RoleAssignmentPolicy::ORDINARY_ROLES,
        ]);
    }

    public function change(): RedirectResponse|ResponseInterface
    {
        $actor = auth()->user();

        if ($actor === null || ! $actor->can('users.assign')) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        $targetId = filter_var($this->request->getPost('user_id'), FILTER_VALIDATE_INT);

        if ($targetId === false || $targetId <= 0) {
            return redirect()->back()->with('error', 'Selecciona un usuario válido.');
        }

        try {
            (new RoleAssignmentService())->change(
                $actor,
                $targetId,
                (string) $this->request->getPost('group'),
                (string) $this->request->getPost('operation'),
                (string) $this->request->getPost('reason'),
            );
        } catch (DomainException|InvalidArgumentException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->to('/app/accesos')->with('message', 'Permisos actualizados y auditados.');
    }
}
