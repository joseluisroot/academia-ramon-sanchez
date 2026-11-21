<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Shield\Config\Auth as ShieldAuth;

/**
 * Autenticación de la academia.
 * Sin registro público: las cuentas se crearán por invitación autorizada.
 */
class Auth extends ShieldAuth
{
    public bool $allowRegistration = false;

    public array $redirects = [
        'register'          => '/app',
        'login'             => '/app',
        'logout'            => 'login',
        'force_reset'       => '/app',
        'permission_denied' => '/sin-acceso',
        'group_denied'      => '/sin-acceso',
    ];

    // Solo personaliza la vista de inicio de sesión; el resto queda en Shield.
    public function __construct()
    {
        parent::__construct();
        $this->views['login'] = 'auth/login';
    }
}
