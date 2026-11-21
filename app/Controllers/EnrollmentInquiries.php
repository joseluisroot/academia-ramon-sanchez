<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\EnrollmentInquiryModel;
use App\Services\EnrollmentInquiryPolicy;
use CodeIgniter\HTTP\RedirectResponse;

final class EnrollmentInquiries extends BaseController
{
    public function submit(): RedirectResponse
    {
        // Campo señuelo oculto para bots; responder igual sin almacenar datos.
        if (trim((string) $this->request->getPost('website')) !== '') {
            return redirect()->to('/#contacto')->with('inquiry_success', true);
        }

        if (! service('throttler')->check('public-inquiry-' . sha1((string) $this->request->getIPAddress()), 3, 3600)) {
            return redirect()->to('/#contacto')->with('inquiry_error', 'Se alcanzó el límite temporal de solicitudes. Intenta más tarde.');
        }

        $name = trim((string) $this->request->getPost('contact_name'));
        $email = mb_strtolower(trim((string) $this->request->getPost('email')));
        $phone = trim((string) $this->request->getPost('phone'));
        $category = (string) $this->request->getPost('category');
        $message = trim((string) $this->request->getPost('message'));

        if (! EnrollmentInquiryPolicy::validName($name)
            || filter_var($email, FILTER_VALIDATE_EMAIL) === false
            || ! EnrollmentInquiryPolicy::validPhone($phone)
            || ! EnrollmentInquiryPolicy::validCategory($category)
            || ! EnrollmentInquiryPolicy::validMessage($message)
            || $this->request->getPost('privacy_consent') !== '1'
        ) {
            return redirect()->to('/#contacto')->with('inquiry_error', 'Revisa los datos y confirma que eres una persona adulta responsable.');
        }

        $model = new EnrollmentInquiryModel();
        $now = date('Y-m-d H:i:s');

        $id = $model->insert([
            'contact_name' => $name,
            'email' => $email,
            'phone' => $phone === '' ? null : $phone,
            'category' => $category,
            'message' => $message === '' ? null : $message,
            'status' => 'new',
            'consented_at' => $now,
            'created_at' => $now,
        ]);

        if ($id === false) {
            return redirect()->to('/#contacto')->with('inquiry_error', 'No se pudo guardar tu consulta. Intenta nuevamente.');
        }

        return redirect()->to('/#contacto')->with('inquiry_success', true);
    }
}
