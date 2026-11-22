<?php

declare(strict_types=1);

namespace App\Services;

use DateTimeImmutable;

final class ConsentAcceptancePolicy
{
    /**
     * Only opaque private archive identifiers. Do not store URLs, paths,
     * signatures, documents or personal information in this field.
     */
    public static function validEvidenceReference(string $reference): bool
    {
        return preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]{5,119}$/D', $reference) === 1;
    }

    public static function validVersion(string $version): bool
    {
        return preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]{0,39}$/D', $version) === 1;
    }

    public static function validDates(string $signedOn, string $expiresOn): bool
    {
        $signed = DateTimeImmutable::createFromFormat('!Y-m-d', $signedOn);
        $today = new DateTimeImmutable('today');

        if ($signed === false || $signed->format('Y-m-d') !== $signedOn || $signed > $today) {
            return false;
        }

        if ($expiresOn === '') {
            return true;
        }

        $expires = DateTimeImmutable::createFromFormat('!Y-m-d', $expiresOn);

        return $expires !== false && $expires->format('Y-m-d') === $expiresOn
            && $expires >= $signed && $expires >= $today;
    }
}
