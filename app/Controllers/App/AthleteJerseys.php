<?php

declare(strict_types=1);

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Services\JerseyAssignmentService;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use Throwable;

final class AthleteJerseys extends BaseController
{
    public function assign(int $athleteId): RedirectResponse|ResponseInterface
    {
        $actor = auth()->user();

        if ($actor === null || ! $actor->can('athletes.manage')) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        $number = filter_var($this->request->getPost('jersey_number'), FILTER_VALIDATE_INT);

        try {
            (new JerseyAssignmentService())->assign($actor, $athleteId, $number === false ? 0 : $number);
        } catch (DomainException $exception) {
            return redirect()->to('/app/atletas/' . $athleteId . '/editar')->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            log_message('error', 'Error al asignar dorsal deportivo.');

            return redirect()->to('/app/atletas/' . $athleteId . '/editar')->with('error', 'No se pudo asignar el dorsal. Comprueba que está disponible.');
        }

        return redirect()->to('/app/atletas/' . $athleteId . '/editar')->with('message', 'Dorsal actualizado correctamente.');
    }

    public function release(int $athleteId): RedirectResponse|ResponseInterface
    {
        $actor = auth()->user();

        if ($actor === null || ! $actor->can('athletes.manage')) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        try {
            (new JerseyAssignmentService())->release($actor, $athleteId);
        } catch (DomainException $exception) {
            return redirect()->to('/app/atletas/' . $athleteId . '/editar')->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            log_message('error', 'Error al liberar dorsal deportivo.');

            return redirect()->to('/app/atletas/' . $athleteId . '/editar')->with('error', 'No se pudo liberar el dorsal.');
        }

        return redirect()->to('/app/atletas/' . $athleteId . '/editar')->with('message', 'Dorsal liberado. El historial se conserva.');
    }
}
