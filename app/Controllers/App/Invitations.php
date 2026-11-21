<?php

declare(strict_types=1);

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Services\InvitationPolicy;
use App\Services\UserInvitationService;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use RuntimeException;

final class Invitations extends BaseController
{
    public function index(): string|ResponseInterface
    {
        if (! auth()->user()?->can('users.invite')) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        return view('app/invitations/index', [
            'title' => 'Invitar personas · Academia Ramón Sánchez',
            'roles' => ['guardian' => 'Padre o responsable', 'coach' => 'Entrenador', 'media_contributor' => 'Colaborador multimedia'],
        ]);
    }

    public function send(): RedirectResponse|ResponseInterface
    {
        $actor = auth()->user();

        if ($actor === null || ! $actor->can('users.invite')) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        // Límite inicial por responsable: hasta 5 solicitudes por hora.
        if (! service('throttler')->check('invite-user-' . (int) $actor->id, 5, 3600)) {
            return redirect()->back()->with('error', 'Has alcanzado el límite temporal de invitaciones.');
        }

        try {
            (new UserInvitationService())->invite(
                $actor,
                (string) $this->request->getPost('email'),
                (string) $this->request->getPost('role'),
            );
        } catch (DomainException|RuntimeException $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->to('/app/invitaciones')
            ->with('message', 'La invitación fue procesada. Verifica su recepción por correo.');
    }
}
