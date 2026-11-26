<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Safe public launch switch.
 *
 * In production, the default is prelaunch. An explicit environment setting
 * 'academy.launchMode = full' is required to expose the completed site.
 * In local/development environments the default is full so WAMP remains usable.
 */
final class SiteLaunchMode
{
    public static function isPrelaunch(): bool
    {
        $default = ENVIRONMENT === 'production' ? 'prelaunch' : 'full';
        $configured = strtolower(trim((string) env('academy.launchMode', $default)));

        // Fail closed on accidental/misspelled values in production.
        return $configured !== 'full';
    }
}
