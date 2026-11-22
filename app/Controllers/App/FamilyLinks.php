<?php

declare(strict_types=1);

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Services\FamilyLinkService;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use Throwable;

final class FamilyLinks extends BaseController
{
    public function index(): string|ResponseInterface
    {
        if (! auth()->user()?->can('family.links.verify')) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        $links = db_connect()->table('athlete_guardians ag')
            ->select('ag.id, ag.relationship, a.first_name AS athlete_first, a.last_name AS athlete_last, p.first_name AS adult_first, p.last_name AS adult_last, p.user_id')
            ->join('athletes a', 'a.id = ag.athlete_id')
            ->join('persons p', 'p.id = ag.person_id')
            ->where('ag.verified_at IS NULL', null, false)
            ->orderBy('ag.id', 'DESC')
            ->limit(50)
            ->get()->getResultArray();

        return view('app/families/verify', [
            'title' => 'Verificar vínculos familiares · Academia Ramón Sánchez',
            'links' => $links,
        ]);
    }

    public function verify(): RedirectResponse|ResponseInterface
    {
        $actor = auth()->user();

        if ($actor === null || ! $actor->can('family.links.verify')) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        $id = filter_var($this->request->getPost('relation_id'), FILTER_VALIDATE_INT);

        try {
            (new FamilyLinkService())->verify(
                $actor,
                $id === false ? 0 : $id,
                (string) $this->request->getPost('email'),
                $this->request->getPost('verified_relationship') === '1',
            );
        } catch (DomainException $exception) {
            return redirect()->to('/app/familias/verificar')->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            log_message('error', 'Fallo de verificación de relación familiar.');

            return redirect()->to('/app/familias/verificar')->with('error', 'No fue posible verificar el vínculo familiar.');
        }

        return redirect()->to('/app/familias/verificar')->with('message', 'Relación verificada. La cuenta ya puede consultar únicamente este atleta.');
    }
}
