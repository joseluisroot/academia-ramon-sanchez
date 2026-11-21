<?php

declare(strict_types=1);

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Models\EnrollmentInquiryModel;
use App\Services\AuditLogger;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use RuntimeException;
use Throwable;

final class EnrollmentInquiries extends BaseController
{
    public function index(): string|ResponseInterface
    {
        if (! auth()->user()?->can('inquiries.view')) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        $model = new EnrollmentInquiryModel();

        return view('app/inquiries/index', [
            'title' => 'Consultas de familias · Academia Ramón Sánchez',
            'inquiries' => $model->orderBy('id', 'DESC')->paginate(20),
            'pager' => $model->pager,
        ]);
    }

    public function contacted(): RedirectResponse|ResponseInterface
    {
        $actor = auth()->user();

        if ($actor === null || ! $actor->can('inquiries.manage')) {
            return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        }

        $id = filter_var($this->request->getPost('inquiry_id'), FILTER_VALIDATE_INT);

        if ($id === false || $id <= 0) {
            return redirect()->to('/app/consultas')->with('error', 'Selecciona una consulta válida.');
        }

        $db = db_connect();
        $db->transBegin();

        try {
            $db->table('enrollment_inquiries')
                ->where('id', $id)
                ->where('status', 'new')
                ->update([
                    'status' => 'contacted',
                    'contacted_by' => (int) $actor->id,
                    'contacted_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            if ($db->affectedRows() !== 1) {
                throw new DomainException('Esta consulta ya fue atendida o no existe.');
            }

            (new AuditLogger())->record(
                (int) $actor->id,
                'inquiry.contacted',
                'enrollment_inquiry',
                (string) $id,
                'Confirmación manual de contacto',
                ['source' => 'portal', 'result' => 'contacted'],
            );

            if ($db->transStatus() === false) {
                throw new RuntimeException('No fue posible actualizar la consulta.');
            }

            $db->transCommit();
        } catch (DomainException $exception) {
            $db->transRollback();

            return redirect()->to('/app/consultas')->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            $db->transRollback();
            log_message('error', 'No se pudo actualizar el estado de una consulta.');

            return redirect()->to('/app/consultas')->with('error', 'No fue posible actualizar la consulta.');
        }

        return redirect()->to('/app/consultas')->with('message', 'Consulta marcada como contactada.');
    }
}
