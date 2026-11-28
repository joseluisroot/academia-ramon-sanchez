<?php

declare(strict_types=1);

namespace App\Services\Tickets;

final class TicketAvailability
{
    public static function enabled(): bool
    {
        return self::parseFlag(env('academy.modules.tickets.enabled', false));
    }

    public static function parseFlag(mixed $value): bool
    {
        return $value === true || (is_string($value) && strtolower(trim($value)) === 'true');
    }

    public static function isModulePath(string $path): bool
    {
        return $path === 'tickets' || str_starts_with($path, 'tickets/');
    }

    /** Exact path/method pairs only. Never open the entire /app namespace. */
    public static function allowsDuringPrelaunch(string $path, string $method): bool
    {
        $allowed = [
            'tickets' => ['GET'],
            'login' => ['GET', 'POST'],
            'logout' => ['GET'],
            'login/magic-link' => ['GET', 'POST'],
            'login/verify-magic-link' => ['GET'],
            'auth/a/show' => ['GET'],
            'auth/a/handle' => ['POST'],
            'auth/a/verify' => ['POST'],
            'sin-acceso' => ['GET'],
        ];

        if ($path === 'tickets/eventos' || preg_match('~^tickets/eventos/[1-9][0-9]*$~D', $path)) {
            return in_array($method, ['GET', 'POST'], true);
        }
        if (preg_match('~^tickets/eventos/[1-9][0-9]*/(?:tipos|estado)$~D', $path)) {
            return $method === 'POST';
        }
        if (preg_match('~^tickets/eventos/[1-9][0-9]*/tipos/[1-9][0-9]*(?:/eliminar)?$~D', $path)) {
            return $method === 'POST';
        }
        return in_array($method, $allowed[$path] ?? [], true);
    }
}
