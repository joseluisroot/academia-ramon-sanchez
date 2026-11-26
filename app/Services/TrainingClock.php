<?php

declare(strict_types=1);

namespace App\Services;

use DateTimeImmutable;
use DateTimeZone;

/**
 * Business dates are local to the academy in El Salvador.
 * Technical timestamps are stored in UTC; legacy timestamps need review if
 * their originating server was not UTC.
 */
final class TrainingClock
{
    private const ZONE = 'America/El_Salvador';

    public static function today(): string
    {
        return (new DateTimeImmutable('now', new DateTimeZone(self::ZONE)))->format('Y-m-d');
    }

    public static function nowUtc(): string
    {
        return (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s');
    }

    public static function displayUtc(string $timestamp): string
    {
        $datetime = DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', $timestamp, new DateTimeZone('UTC'));
        if ($datetime === false || $datetime->format('Y-m-d H:i:s') !== $timestamp) {
            return $timestamp;
        }
        return $datetime->setTimezone(new DateTimeZone(self::ZONE))->format('Y-m-d H:i:s');
    }
}
