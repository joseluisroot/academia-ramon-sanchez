<?php

declare(strict_types=1);

namespace App\Controllers\App;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * La relación cuenta-adulto-atleta se comprueba en cada consulta, no se
 * aceptan IDs elegidos por el navegador ni existe listado global.
 */
final class MyAthletes extends BaseController
{
    public function index(): string|ResponseInterface
    {
        $user = auth()->user();

        if ($user === null || ! $user->inGroup('guardian')) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        $rows = db_connect()->table('athletes a')
            ->select('a.id, a.first_name, a.last_name, a.status, c.code AS category_code')
            ->join('sports_categories c', 'c.id = a.sports_category_id')
            ->join('athlete_guardians ag', 'ag.athlete_id = a.id')
            ->join('persons p', 'p.id = ag.person_id')
            ->where('p.user_id', (int) $user->id)
            ->where('p.status', 'active')
            ->where('ag.verified_at IS NOT NULL', null, false)
            ->where('a.status', 'active')
            ->distinct()
            ->orderBy('a.first_name')
            ->get()
            ->getResultArray();

        return view('app/families/my_athletes', [
            'title' => 'Mis atletas · Academia Ramón Sánchez',
            'athletes' => $rows,
        ]);
    }
}
