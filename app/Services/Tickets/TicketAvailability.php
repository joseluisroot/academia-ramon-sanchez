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

        return in_array($method, $allowed[$path] ?? [], true);
    }
}
