<?php

declare(strict_types=1);

namespace App\Controllers\App;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Dashboard extends BaseController
{
    /**
     * El acceso se controla mediante los filtros session y portal.access
     * definidos explícitamente en las rutas del módulo privado.
     */
    public function index(): string|ResponseInterface
    {
        $user = auth()->user();

        if ($user === null || ! $user->can('portal.access')) {
            return service('response')->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        return view('app/dashboard', [
            'title'    => 'Panel principal · Academia Ramón Sánchez',
            'username' => (string) ($user->username ?? 'Usuario'),
            'groups'   => $user->getGroups(),
        ]);
    }
}
