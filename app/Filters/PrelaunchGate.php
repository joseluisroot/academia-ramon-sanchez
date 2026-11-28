<?php

declare(strict_types=1);

namespace App\Filters;

use App\Services\SiteLaunchMode;
use App\Services\Tickets\TicketAvailability;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * Entire unfinished CI4 portal is closed to the public in prelaunch mode.
 * Apache-served static assets remain public; no personal material should
 * be placed in public/assets or in uploaded build artifacts.
 */
final class PrelaunchGate implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $path = trim($request->getUri()->getPath(), '/');
        $method = strtoupper($request->getMethod());

        // The module flag is independent of the general launch mode.
        if (TicketAvailability::isModulePath($path) && ! TicketAvailability::enabled()) {
            return service('response')->setStatusCode(404)
                ->setHeader('Cache-Control', 'no-store, private')
                ->setBody('Página no disponible.');
        }

        if (! SiteLaunchMode::isPrelaunch()) {
            return null;
        }

        if (TicketAvailability::enabled() && TicketAvailability::allowsDuringPrelaunch($path, $method)) {
            return null;
        }
        if ($path === '' && strtoupper($request->getMethod()) === 'GET') {
            return null;
        }

        $response = service('response');
        $response->setHeader('Cache-Control', 'no-store, private');
        $response->setHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');

        // In particular, POST/PUT/DELETE are not routed to unfinished features.
        if (strtoupper($request->getMethod()) !== 'GET') {
            return $response->setStatusCode(404)->setBody('Página no disponible.');
        }

        return $response->redirect(site_url('/'), 'location', 302);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
