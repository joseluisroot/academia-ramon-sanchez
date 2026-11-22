<?php

declare(strict_types=1);

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Services\AthleteLifecycleService;
use App\Services\AthleteConsentRegisterService;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use Throwable;

final class AthleteAdministration extends BaseController
{
    public function status(int $athleteId): RedirectResponse|ResponseInterface
    {
        $actor = auth()->user();
        if ($actor === null || ! $actor->can('athletes.manage')) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        try {
            (new AthleteLifecycleService())->change(
                $actor,
                $athleteId,
                (string) $this->request->getPost('status'),
                (string) $this->request->getPost('reason'),
            );
        } catch (DomainException $exception) {
            return redirect()->to('/app/atletas/' . $athleteId . '/editar')->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            log_message('error', 'No se pudo registrar transición de estado del atleta.');
            return redirect()->to('/app/atletas/' . $athleteId . '/editar')
                ->with('error', 'No fue posible cambiar el estado.');
        }

        return redirect()->to('/app/atletas/' . $athleteId . '/editar')
            ->with('message', 'Estado del atleta actualizado y registrado en su historial.');
    }

    public function consent(int $athleteId): RedirectResponse|ResponseInterface
    {
        $actor = auth()->user();
        if ($actor === null || ! $actor->can('athletes.manage')) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        try {
            (new AthleteConsentRegisterService())->record(
                $actor,
                $athleteId,
                (string) $this->request->getPost('type'),
                (string) $this->request->getPost('state'),
            );
        } catch (DomainException $exception) {
            return redirect()->to('/app/atletas/' . $athleteId . '/editar')->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            log_message('error', 'No se pudo registrar el estado de documentación del atleta.');
            return redirect()->to('/app/atletas/' . $athleteId . '/editar')
                ->with('error', 'No fue posible actualizar el estado documental.');
        }

        return redirect()->to('/app/atletas/' . $athleteId . '/editar')
            ->with('message', 'Estado de documentación registrado. Esto no autoriza publicaciones ni tratamiento adicional de datos.');
    }
}
