<?php

declare(strict_types=1);

namespace App\Services;

use DateTimeImmutable;

final class AthleteRegistrationPolicy
{
    public const RELATIONSHIPS = ['mother', 'father', 'legal_guardian', 'other_authorized'];

    public static function validName(string $value): bool
    {
        $length = mb_strlen(trim($value));

        return $length >= 2 && $length <= 100;
    }

    public static function validBirthAndEntry(string $birthDate, string $joinedOn): bool
    {
        $birth = DateTimeImmutable::createFromFormat('!Y-m-d', $birthDate);
        $joined = DateTimeImmutable::createFromFormat('!Y-m-d', $joinedOn);
        $today = new DateTimeImmutable('today');

        return $birth !== false
            && $joined !== false
            && $birth->format('Y-m-d') === $birthDate
            && $joined->format('Y-m-d') === $joinedOn
            && $birth <= $joined
            && $joined <= $today
            && $birth->diff($joined)->y < 18;
    }

    public static function validRelationship(string $relationship): bool
    {
        return in_array($relationship, self::RELATIONSHIPS, true);
    }

    public static function validPhone(string $phone): bool
    {
        return $phone === '' || (mb_strlen($phone) <= 30 && preg_match('/^[0-9+() .-]{7,30}$/', $phone) === 1);
    }
}
