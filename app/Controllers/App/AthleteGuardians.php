<?php

declare(strict_types=1);

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Services\AthleteGuardianService;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use Throwable;

final class AthleteGuardians extends BaseController
{
    public function add(int $athleteId): RedirectResponse|ResponseInterface
    {
        $actor = auth()->user();

        if ($actor === null || ! $actor->can('athletes.manage')) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        $personId = filter_var($this->request->getPost('person_id'), FILTER_VALIDATE_INT);

        try {
            (new AthleteGuardianService())->add(
                $actor,
                $athleteId,
                $personId === false ? 0 : $personId,
                (string) $this->request->getPost('relationship'),
            );
        } catch (DomainException $exception) {
            return redirect()->to('/app/atletas/' . $athleteId . '/editar')->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            log_message('error', 'Error al añadir otro responsable familiar.');

            return redirect()->to('/app/atletas/' . $athleteId . '/editar')->with('error', 'No se pudo registrar el nuevo responsable.');
        }

        return redirect()->to('/app/atletas/' . $athleteId . '/editar')->with('message', 'Responsable añadido. Su acceso familiar queda pendiente de verificación.');
    }

    public function revoke(int $athleteId): RedirectResponse|ResponseInterface
    {
        $actor = auth()->user();

        if ($actor === null || ! $actor->can('family.links.verify')) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        $relationId = filter_var($this->request->getPost('relation_id'), FILTER_VALIDATE_INT);

        if ($relationId === false || $relationId <= 0) {
            return redirect()->to('/app/atletas/' . $athleteId . '/editar')->with('error', 'Selecciona una relación familiar válida.');
        }

        // Evitar que un administrador manipule un ID de relación de otro atleta.
        $matched = db_connect()->table('athlete_guardians')
            ->where('id', $relationId)
            ->where('athlete_id', $athleteId)->countAllResults();

        if ($matched !== 1) {
            return $this->response->setStatusCode(404)->setBody('Relación no encontrada.');
        }

        try {
            (new AthleteGuardianService())->revoke($actor, $relationId);
        } catch (DomainException $exception) {
            return redirect()->to('/app/atletas/' . $athleteId . '/editar')->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            log_message('error', 'Error al revocar vínculo familiar.');

            return redirect()->to('/app/atletas/' . $athleteId . '/editar')->with('error', 'No se pudo revocar el acceso familiar.');
        }

        return redirect()->to('/app/atletas/' . $athleteId . '/editar')->with('message', 'Se revocó el acceso del responsable a este atleta.');
    }
}
