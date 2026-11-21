<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Reglas puras de autorización para asignar roles.
 * La validación por permiso de actor se realiza además en el servicio.
 */
final class RoleAssignmentPolicy
{
    /** @var list<string> */
    public const ORDINARY_ROLES = [
        'guardian',
        'coach',
        'media_contributor',
    ];

    /** @var list<string> */
    public const PROTECTED_ROLES = [
        'institutional_admin',
        'access_manager',
        'technical_support',
    ];

    public static function canAssign(string $group): bool
    {
        return in_array($group, self::ORDINARY_ROLES, true);
    }

    /**
     * @param array<string> $groups
     */
    public static function isProtectedAccount(array $groups): bool
    {
        return array_intersect(self::PROTECTED_ROLES, $groups) !== [];
    }
}
