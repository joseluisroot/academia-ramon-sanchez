<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Dedicated prelaunch branch: the homepage defaults to the teaser in
 * development AND production. Explicitly use academy.launchMode = full
 * only when intentionally previewing the unfinished full website.
 */
final class SiteLaunchMode
{
    public static function isPrelaunch(): bool
    {
        return strtolower(trim((string) env('academy.launchMode', 'prelaunch'))) !== 'full';
    }
}
