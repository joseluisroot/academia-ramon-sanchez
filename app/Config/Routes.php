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

// TKT-001: workspace independiente, sin permiso portal.access.
$routes->get('tickets', 'App\\Tickets\\Workspace::index', [
    'as' => 'tickets.workspace',
    'filter' => ['session'],
]);

// TKT-002: explicit event administration routes.
$routes->group('tickets/eventos', ['filter' => ['session']], static function (RouteCollection $routes): void {
    $routes->get('', 'App\\Tickets\\Events::index');
    $routes->post('', 'App\\Tickets\\Events::create');
    $routes->get('(:num)', 'App\\Tickets\\Events::show/$1');
    $routes->post('(:num)', 'App\\Tickets\\Events::update/$1');
    $routes->post('(:num)/tipos', 'App\\Tickets\\Events::addType/$1');
    $routes->post('(:num)/emision/preview', 'App\\Tickets\\TicketPreview::create/$1');
    $routes->get('(:num)/emision', 'App\\Tickets\\Issuance::index/$1');
    $routes->post('(:num)/emision', 'App\\Tickets\\Issuance::create/$1');
    $routes->post('(:num)/estado', 'App\\Tickets\\Events::changeStatus/$1');
    $routes->post('(:num)/tipos/(:num)', 'App\\Tickets\\Events::updateType/$1/$2');
    $routes->post('(:num)/tipos/(:num)/eliminar', 'App\\Tickets\\Events::deleteType/$1/$2');
});

// El portal privado exige sesión y permiso: no basta con tener una cuenta.
$routes->group('app', ['filter' => ['session', 'permission:portal.access']], static function (RouteCollection $routes): void {
    $routes->get('/', 'App\\Dashboard::index', ['as' => 'app.dashboard']);

    // INC-004: primer corte de entrenamiento; autorización adicional por grupo
    // institucional dentro del servicio/controlador (no acceso global de entrenadores).
    $routes->get('entrenamientos/entrenadores', 'App\\CoachAssignments::index', ['as' => 'app.training.coach_assignments', 'filter' => 'permission:training.assign']);
    $routes->post('entrenamientos/entrenadores', 'App\\CoachAssignments::change', ['as' => 'app.training.coach_assignments.change', 'filter' => 'permission:training.assign']);
    $routes->get('entrenamientos', 'App\\TrainingSessions::index', ['as' => 'app.training.index', 'filter' => 'permission:training.manage']);
    $routes->post('entrenamientos', 'App\\TrainingSessions::create', ['as' => 'app.training.create', 'filter' => 'permission:training.manage']);
    $routes->get('entrenamientos/reporte', 'App\\TrainingSessions::report', ['as' => 'app.training.report', 'filter' => 'permission:training.manage']);
    $routes->get('entrenamientos/(:num)', 'App\\TrainingSessions::show/$1', ['as' => 'app.training.show', 'filter' => 'permission:training.manage']);
    $routes->post('entrenamientos/(:num)/estado', 'App\\TrainingSessions::changeStatus/$1', ['as' => 'app.training.status', 'filter' => 'permission:training.manage']);
    $routes->post('entrenamientos/(:num)/asistencia', 'App\\TrainingSessions::attendance/$1', ['as' => 'app.training.attendance', 'filter' => 'permission:training.manage']);

    // El expediente global requiere autorización administrativa expresa.
    $routes->get('atletas', 'App\\Athletes::index', ['as' => 'app.athletes.index', 'filter' => 'permission:athletes.manage']);
    $routes->get('atletas/nuevo', 'App\\Athletes::create', ['as' => 'app.athletes.create', 'filter' => 'permission:athletes.manage']);
    $routes->post('atletas', 'App\\Athletes::store', ['as' => 'app.athletes.store', 'filter' => 'permission:athletes.manage']);
    $routes->get('atletas/(:num)/editar', 'App\\Athletes::edit/$1', ['as' => 'app.athletes.edit', 'filter' => 'permission:athletes.manage']);
    $routes->post('atletas/(:num)', 'App\\Athletes::update/$1', ['as' => 'app.athletes.update', 'filter' => 'permission:athletes.manage']);

    // Estados del expediente y registro documental, con autorización administrativa.
    $routes->post('atletas/(:num)/estado', 'App\\AthleteAdministration::status/$1', ['as' => 'app.athletes.status', 'filter' => 'permission:athletes.manage']);
    $routes->post('atletas/(:num)/documentacion', 'App\\AthleteAdministration::consent/$1', ['as' => 'app.athletes.documentation', 'filter' => 'permission:athletes.manage']);

    // Aceptaciones documentadas: autorización institucional diferenciada.
    $routes->post('atletas/(:num)/documentacion/aceptacion', 'App\\AthleteAdministration::acceptance/$1', ['as' => 'app.athletes.consent.acceptance', 'filter' => 'permission:consents.verify']);

    // Dorsales por categoría y nuevos responsables, siempre con permisos del servidor.
    $routes->post('atletas/(:num)/dorsal', 'App\\AthleteJerseys::assign/$1', ['as' => 'app.athletes.jersey.assign', 'filter' => 'permission:athletes.manage']);
    $routes->post('atletas/(:num)/dorsal/liberar', 'App\\AthleteJerseys::release/$1', ['as' => 'app.athletes.jersey.release', 'filter' => 'permission:athletes.manage']);
    $routes->post('atletas/(:num)/responsables', 'App\\AthleteGuardians::add/$1', ['as' => 'app.athletes.guardians.add', 'filter' => 'permission:athletes.manage']);
    $routes->post('atletas/(:num)/responsables/nuevo', 'App\\AthleteGuardians::addNew/$1', ['as' => 'app.athletes.guardians.new', 'filter' => 'permission:athletes.manage']);
    $routes->post('atletas/(:num)/responsables/quitar-pendiente', 'App\\AthleteGuardians::removePending/$1', ['as' => 'app.athletes.guardians.remove_pending', 'filter' => 'permission:family.links.verify']);
    $routes->post('atletas/(:num)/responsables/revocar', 'App\\AthleteGuardians::revoke/$1', ['as' => 'app.athletes.guardians.revoke', 'filter' => 'permission:family.links.verify']);

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
