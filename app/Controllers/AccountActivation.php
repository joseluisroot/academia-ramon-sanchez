<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\UserInvitationService;
use CodeIgniter\HTTP\RedirectResponse;
use DomainException;
use RuntimeException;

final class AccountActivation extends BaseController
{
    public function show(string $token): string
    {
        $this->response->setHeader('Cache-Control', 'no-store, private, max-age=0');
        $this->response->setHeader('Referrer-Policy', 'no-referrer');

        return view('auth/activate', [
            'valid' => (new UserInvitationService())->findValid($token) !== null,
            'token' => $token,
        ]);
    }

    public function accept(string $token): RedirectResponse
    {
        if ((string) $this->request->getPost('password') === ''
            || (string) $this->request->getPost('password_confirmation') === '') {
            return redirect()->to(site_url('activar-cuenta/' . $token))
                ->with('error', 'Completa la contraseña y su confirmación.');
        }

        try {
            (new UserInvitationService())->activate(
                $token,
                (string) $this->request->getPost('username'),
                (string) $this->request->getPost('password'),
                (string) $this->request->getPost('password_confirmation'),
            );
        } catch (DomainException|RuntimeException $exception) {
            return redirect()->to(site_url('activar-cuenta/' . $token))
                ->with('error', $exception->getMessage());
        }

        return redirect()->to('/login')
            ->with('message', 'Cuenta activada. Ya puedes ingresar con tu correo y contraseña.');
    }
}
