<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Las invitaciones iniciales solo conceden grupos ordinarios.
 * Los permisos sensibles requieren autorización institucional aparte.
 */
final class InvitationPolicy
{
    public const HOURS_VALID = 48;

    public static function canInviteTo(string $role): bool
    {
        return RoleAssignmentPolicy::canAssign($role);
    }

    public static function validToken(string $token): bool
    {
        return preg_match('/\\A[a-f0-9]{64}\\z/D', $token) === 1;
    }

    public static function validUsername(string $username): bool
    {
        return preg_match('/\\A[a-zA-Z0-9.]{3,30}\\z/D', $username) === 1;
    }

    public static function validPassword(string $password, string $confirmation, string $username): bool
    {
        return $password === $confirmation
            && strlen($password) >= 12
            && strlen($password) <= 128
            && preg_match('/[a-z]/', $password) === 1
            && preg_match('/[A-Z]/', $password) === 1
            && preg_match('/[0-9]/', $password) === 1
            && preg_match('/[^a-zA-Z0-9]/', $password) === 1
            && stripos($password, $username) === false;
    }
}
