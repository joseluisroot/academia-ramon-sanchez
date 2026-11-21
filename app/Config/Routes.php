<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Manejador de rutas inexistentes con respuesta HTTP 404.
$routes->set404Override(static function (): string {
    // Evitar volver a lanzar una excepción al crear un controlador durante el 404.
    // CodeIgniter conserva la respuesta HTTP 404 para el override.
    return view('errors/institutional', [
        'statusCode' => 404,
        'eyebrow' => 'NOS SALIMOS DE LA CANCHA',
        'title' => 'No encontramos esa jugada.',
        'message' => 'La página que buscas no existe o el enlace ya no está disponible. Regresa al inicio para continuar tu recorrido con la academia.',
        'illustration' => 'not-found.svg',
        'illustrationAlt' => 'Recorrido de fútbol con una lupa que muestra el número 404.',
    ]);
});

// Ruta pública sin filtro para evitar bucles de redirección por falta de permisos.
$routes->get('sin-acceso', 'ErrorPages::forbidden', ['as' => 'error.forbidden']);

// Rutas públicas. No se permite el registro autónomo de usuarios.
$routes->get('/', 'Home::index');

// Aviso informativo para quienes utilizan el formulario público.
$routes->get('privacidad', static fn (): string => view('public/privacy'), ['as' => 'public.privacy']);

// Contacto público de responsables adultos, sin registro de menores.
$routes->post('solicitar-informacion', 'EnrollmentInquiries::submit', ['as' => 'public.inquiry.submit']);

// Shield proporciona login, logout, enlace mágico y acciones de autenticación.
// La ruta /register (GET y POST) queda explícitamente excluida.
service('auth')->routes($routes, ['except' => ['register']]);

// Activación por invitación individual: no existe registro público.
$routes->get('activar-cuenta/(:segment)', 'AccountActivation::show/$1', ['as' => 'account.activation.show']);
$routes->post('activar-cuenta/(:segment)', 'AccountActivation::accept/$1', ['as' => 'account.activation.accept']);

// El portal privado exige sesión y permiso: no basta con tener una cuenta.
$routes->group('app', ['filter' => ['session', 'permission:portal.access']], static function (RouteCollection $routes): void {
    $routes->get('/', 'App\\Dashboard::index', ['as' => 'app.dashboard']);

    // El expediente global requiere autorización administrativa expresa.
    $routes->get('atletas', 'App\\Athletes::index', ['as' => 'app.athletes.index', 'filter' => 'permission:athletes.manage']);
    $routes->get('atletas/nuevo', 'App\\Athletes::create', ['as' => 'app.athletes.create', 'filter' => 'permission:athletes.manage']);
    $routes->post('atletas', 'App\\Athletes::store', ['as' => 'app.athletes.store', 'filter' => 'permission:athletes.manage']);

    // Verificación de parentesco exclusiva de administradores institucionales.
    $routes->get('familias/verificar', 'App\\FamilyLinks::index', ['as' => 'app.family.verify.index', 'filter' => 'permission:family.links.verify']);
    $routes->post('familias/verificar', 'App\\FamilyLinks::verify', ['as' => 'app.family.verify.store', 'filter' => 'permission:family.links.verify']);

    // La consulta familiar filtra por usuario activo y vínculo verificado.
    $routes->get('mis-atletas', 'App\\MyAthletes::index', ['as' => 'app.family.athletes', 'filter' => 'permission:family.athletes.view']);

    // Consultas de primera clase: acceso limitado a administración y utilero autorizado.
    $routes->get('consultas', 'App\\EnrollmentInquiries::index', ['as' => 'app.inquiries.index', 'filter' => 'permission:inquiries.view']);
    $routes->post('consultas/contactada', 'App\\EnrollmentInquiries::contacted', ['as' => 'app.inquiries.contacted', 'filter' => 'permission:inquiries.manage']);

    // Invitaciones individuales controladas por el responsable designado.
    $routes->get('invitaciones', 'App\\Invitations::index', ['as' => 'app.invitations.index', 'filter' => 'permission:users.invite']);
    $routes->post('invitaciones', 'App\\Invitations::send', ['as' => 'app.invitations.send', 'filter' => 'permission:users.invite']);
    $routes->post('invitaciones/cancelar', 'App\\Invitations::cancel', ['as' => 'app.invitations.cancel', 'filter' => 'permission:users.invite']);

    // Acceso a roles ordinarios solo para el responsable designado.
    $routes->get('accesos', 'App\\AccessManagement::index', ['as' => 'app.access.index', 'filter' => 'permission:users.assign']);
    $routes->post('accesos', 'App\\AccessManagement::change', ['as' => 'app.access.change', 'filter' => 'permission:users.assign']);
});
