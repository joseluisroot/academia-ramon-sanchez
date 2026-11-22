<?php

declare(strict_types=1);

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Models\AthleteModel;
use App\Models\PersonModel;
use App\Models\SportsCategoryModel;
use App\Services\AthleteRegistrationService;
use App\Services\AthleteRecordService;
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


    public function edit(int $id): string|ResponseInterface
    {
        if (! auth()->user()?->can('athletes.manage')) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        $athlete = (new AthleteModel())->find($id);

        if ($athlete === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $history = db_connect()->table('athlete_category_history h')
            ->select('h.effective_on, h.reason, h.created_at, c.code AS category_code')
            ->join('sports_categories c', 'c.id = h.sports_category_id')
            ->where('h.athlete_id', $id)
            ->orderBy('h.effective_on', 'DESC')
            ->orderBy('h.id', 'DESC')
            ->get()->getResultArray();

        $db = db_connect();

        $currentJersey = $db->table('jersey_assignments')
            ->where('athlete_id', $id)->get()->getRowArray();

        $jerseyHistory = $db->table('jersey_assignment_history h')
            ->select('h.jersey_number, h.event, h.occurred_at, c.code AS category_code')
            ->join('sports_categories c', 'c.id = h.sports_category_id')
            ->where('h.athlete_id', $id)
            ->orderBy('h.id', 'DESC')
            ->limit(25)->get()->getResultArray();

        $guardians = $db->table('athlete_guardians ag')
            ->select('ag.id, ag.relationship, ag.is_primary, ag.verified_at, p.first_name, p.last_name')
            ->join('persons p', 'p.id = ag.person_id')
            ->where('ag.athlete_id', $id)
            ->orderBy('ag.is_primary', 'DESC')
            ->orderBy('ag.id', 'ASC')
            ->get()->getResultArray();

        $statusHistory = $db->table('athlete_status_history')
            ->select('from_status, to_status, reason_code, changed_at')
            ->where('athlete_id', $id)
            ->orderBy('id', 'DESC')
            ->limit(30)->get()->getResultArray();

        $consentHistory = $db->table('athlete_consent_register acr')
            ->select('acr.consent_type, acr.record_state, acr.document_version, acr.signed_on, acr.expires_on, acr.recorded_at, acr.guardian_relation_id, acr.evidence_reference')
            ->where('acr.athlete_id', $id)
            ->orderBy('acr.id', 'DESC')
            ->limit(30)->get()->getResultArray();

        return view('app/athletes/edit', [
            'title' => 'Editar atleta · Academia Ramón Sánchez',
            'athlete' => $athlete,
            'categories' => (new SportsCategoryModel())->where('active', 1)->orderBy('code')->findAll(),
            'history' => $history,
            'currentJersey' => $currentJersey,
            'jerseyHistory' => $jerseyHistory,
            'statusHistory' => $statusHistory,
            'consentHistory' => $consentHistory,
            'consentSigners' => $db->table('athlete_guardians ag')
                ->select('ag.id, ag.relationship, p.first_name, p.last_name')
                ->join('persons p', 'p.id = ag.person_id')
                ->where('ag.athlete_id', $id)
                ->where('p.status', 'active')
                ->orderBy('ag.id', 'ASC')
                ->get()->getResultArray(),
            'guardians' => $guardians,
            'adults' => (new PersonModel())->where('status', 'active')->orderBy('last_name')->orderBy('first_name')->findAll(),
        ]);
    }

    public function update(int $id): RedirectResponse|ResponseInterface
    {
        $actor = auth()->user();

        if ($actor === null || ! $actor->can('athletes.manage')) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        $categoryId = filter_var($this->request->getPost('sports_category_id'), FILTER_VALIDATE_INT);

        try {
            (new AthleteRecordService())->update(
                $actor,
                $id,
                (string) $this->request->getPost('first_name'),
                (string) $this->request->getPost('last_name'),
                $categoryId === false ? 0 : $categoryId,
            );
        } catch (DomainException $exception) {
            return redirect()->to('app/atletas/' . $id . '/editar')->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            log_message('error', 'No se pudo actualizar el expediente de atleta.');

            return redirect()->to('app/atletas/' . $id . '/editar')->with('error', 'No se pudo actualizar el expediente.');
        }

        return redirect()->to('app/atletas/' . $id . '/editar')
            ->with('message', 'Expediente actualizado. El historial conserva las categorías anteriores.');
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
