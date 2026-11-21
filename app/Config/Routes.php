<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Manejador de rutas inexistentes con respuesta HTTP 404.
$routes->set404Override([\App\Controllers\ErrorPages::class, 'notFound']);

// Ruta pública sin filtro para evitar bucles de redirección por falta de permisos.
$routes->get('sin-acceso', 'ErrorPages::forbidden', ['as' => 'error.forbidden']);

// Rutas públicas. No se permite el registro autónomo de usuarios.
$routes->get('/', 'Home::index');

// Shield proporciona login, logout, enlace mágico y acciones de autenticación.
// La ruta /register (GET y POST) queda explícitamente excluida.
service('auth')->routes($routes, ['except' => ['register']]);

// Activación por invitación individual: no existe registro público.
$routes->get('activar-cuenta/(:segment)', 'AccountActivation::show/$1', ['as' => 'account.activation.show']);
$routes->post('activar-cuenta/(:segment)', 'AccountActivation::accept/$1', ['as' => 'account.activation.accept']);

// El portal privado exige sesión y permiso: no basta con tener una cuenta.
$routes->group('app', ['filter' => ['session', 'permission:portal.access']], static function (RouteCollection $routes): void {
    $routes->get('/', 'App\\Dashboard::index', ['as' => 'app.dashboard']);

    // Invitaciones individuales controladas por el responsable designado.
    $routes->get('invitaciones', 'App\\Invitations::index', ['as' => 'app.invitations.index', 'filter' => 'permission:users.invite']);
    $routes->post('invitaciones', 'App\\Invitations::send', ['as' => 'app.invitations.send', 'filter' => 'permission:users.invite']);
    $routes->post('invitaciones/cancelar', 'App\\Invitations::cancel', ['as' => 'app.invitations.cancel', 'filter' => 'permission:users.invite']);

    // Acceso a roles ordinarios solo para el responsable designado.
    $routes->get('accesos', 'App\\AccessManagement::index', ['as' => 'app.access.index', 'filter' => 'permission:users.assign']);
    $routes->post('accesos', 'App\\AccessManagement::change', ['as' => 'app.access.change', 'filter' => 'permission:users.assign']);
});
