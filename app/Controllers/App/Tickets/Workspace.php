<?php

declare(strict_types=1);

namespace App\Controllers\App\Tickets;

use App\Controllers\BaseController;
use App\Services\Tickets\TicketAvailability;
use CodeIgniter\HTTP\ResponseInterface;

final class Workspace extends BaseController
{
    public function index(): ResponseInterface
    {
        $response = $this->response
            ->setHeader('Cache-Control', 'no-store, private, max-age=0')
            ->setHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');

        if (! TicketAvailability::enabled()) {
            return $response->setStatusCode(404)->setBody('Página no disponible.');
        }

        $user = auth()->user();
        if ($user === null || ! $user->can('tickets.access')) {
            return $response->setStatusCode(403)->setBody('Acceso no autorizado a ARS Tickets.');
        }

        return $response->setBody(view('app/tickets/workspace', [
            'canManageEvents' => $user->inGroup('institutional_admin') && $user->can('events.manage'),
            'username' => (string) ($user->username ?? 'Operador'),
        ]));
    }
}
