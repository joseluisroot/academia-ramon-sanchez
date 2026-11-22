<?php

declare(strict_types=1);

use App\Services\ConsentAcceptancePolicy;
use PHPUnit\Framework\TestCase;

final class ConsentAcceptancePolicyTest extends TestCase
{
    public function testEvidenceReferenceIsOpaqueAndBounded(): void
    {
        self::assertTrue(ConsentAcceptancePolicy::validEvidenceReference('CONS-2026-0001'));
        self::assertFalse(ConsentAcceptancePolicy::validEvidenceReference('abc'));
        self::assertFalse(ConsentAcceptancePolicy::validEvidenceReference('https://example.org/evidencia.pdf'));
        self::assertFalse(ConsentAcceptancePolicy::validEvidenceReference('../file'));
        self::assertFalse(ConsentAcceptancePolicy::validEvidenceReference(str_repeat('A', 121)));
    }

    public function testVersionIsRequiredAndStructured(): void
    {
        self::assertTrue(ConsentAcceptancePolicy::validVersion('V1-2026'));
        self::assertFalse(ConsentAcceptancePolicy::validVersion(''));
        self::assertFalse(ConsentAcceptancePolicy::validVersion('v1/2026'));
        self::assertFalse(ConsentAcceptancePolicy::validVersion(str_repeat('A', 41)));
    }

    public function testSignatureDatesAreConsistent(): void
    {
        self::assertTrue(ConsentAcceptancePolicy::validDates('2026-11-20', ''));
        self::assertTrue(ConsentAcceptancePolicy::validDates('2026-11-20', '2030-12-01'));
        self::assertFalse(ConsentAcceptancePolicy::validDates('2026-02-30', ''));
        self::assertFalse(ConsentAcceptancePolicy::validDates('2030-01-01', ''));
        self::assertFalse(ConsentAcceptancePolicy::validDates('2026-11-20', '2026-11-01'));
    }
}
