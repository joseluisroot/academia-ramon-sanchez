<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Rutas públicas. No se permite el registro autónomo de usuarios.
$routes->get('/', 'Home::index');

// Shield proporciona login, logout, enlace mágico y acciones de autenticación.
// La ruta /register (GET y POST) queda explícitamente excluida.
service('auth')->routes($routes, ['except' => ['register']]);

// El portal privado exige sesión y permiso: no basta con tener una cuenta.
$routes->group('app', ['filter' => ['session', 'permission:portal.access']], static function (RouteCollection $routes): void {
    $routes->get('/', 'App\\Dashboard::index', ['as' => 'app.dashboard']);
});
