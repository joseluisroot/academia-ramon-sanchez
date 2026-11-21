<?php

declare(strict_types=1);

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Models\AthleteModel;
use App\Models\PersonModel;
use App\Models\SportsCategoryModel;
use App\Services\AthleteRegistrationService;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use RuntimeException;
use Throwable;

final class Athletes extends BaseController
{
    /**
     * El listado del módulo es solo administrativo: no debe exponerse
     * a padres ni a personal que únicamente tiene athletes.view.
     */
    public function index(): string|ResponseInterface
    {
        if (! auth()->user()?->can('athletes.manage')) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        $athletes = new AthleteModel();

        return view('app/athletes/index', [
            'title' => 'Atletas · Academia Ramón Sánchez',
            'athletes' => $athletes
                ->select('athletes.id, athletes.first_name, athletes.last_name, athletes.joined_on, athletes.status, sports_categories.code AS category_code')
                ->join('sports_categories', 'sports_categories.id = athletes.sports_category_id')
                ->orderBy('athletes.id', 'DESC')
                ->paginate(20),
            'pager' => $athletes->pager,
        ]);
    }

    public function create(): string|ResponseInterface
    {
        if (! auth()->user()?->can('athletes.manage')) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        return view('app/athletes/create', [
            'title' => 'Registrar atleta · Academia Ramón Sánchez',
            'categories' => (new SportsCategoryModel())->where('active', 1)->orderBy('code')->findAll(),
            'adults' => (new PersonModel())->where('status', 'active')->orderBy('last_name')->orderBy('first_name')->findAll(),
        ]);
    }

    public function store(): RedirectResponse|ResponseInterface
    {
        $actor = auth()->user();

        if ($actor === null || ! $actor->can('athletes.manage')) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        try {
            (new AthleteRegistrationService())->register($actor, [
                'first_name' => (string) $this->request->getPost('first_name'),
                'last_name' => (string) $this->request->getPost('last_name'),
                'birth_date' => (string) $this->request->getPost('birth_date'),
                'joined_on' => (string) $this->request->getPost('joined_on'),
                'sports_category_id' => (string) $this->request->getPost('sports_category_id'),
                'existing_person_id' => (string) $this->request->getPost('existing_person_id'),
                'guardian_first_name' => (string) $this->request->getPost('guardian_first_name'),
                'guardian_last_name' => (string) $this->request->getPost('guardian_last_name'),
                'guardian_phone' => (string) $this->request->getPost('guardian_phone'),
                'relationship' => (string) $this->request->getPost('relationship'),
            ]);
        } catch (DomainException $exception) {
            return redirect()->to('/app/atletas/nuevo')->with('error', $exception->getMessage());
        } catch (RuntimeException $exception) {
            log_message('error', 'Fallo controlado de inscripción de atleta.');

            return redirect()->to('/app/atletas/nuevo')->with('error', 'No fue posible completar la inscripción.');
        } catch (Throwable $exception) {
            log_message('error', 'Fallo inesperado al inscribir un atleta.');

            return redirect()->to('/app/atletas/nuevo')->with('error', 'No fue posible completar la inscripción.');
        }

        return redirect()->to('/app/atletas')->with('message', 'Atleta registrado. El vínculo digital del responsable queda pendiente de verificación.');
    }
}
