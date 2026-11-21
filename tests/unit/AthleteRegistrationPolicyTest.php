<?php

declare(strict_types=1);

use App\Services\AthleteRegistrationPolicy;
use PHPUnit\Framework\TestCase;

final class AthleteRegistrationPolicyTest extends TestCase
{
    public function testNamesAndResponsibleRelationship(): void
    {
        self::assertTrue(AthleteRegistrationPolicy::validName('Lucas'));
        self::assertFalse(AthleteRegistrationPolicy::validName('A'));
        self::assertFalse(AthleteRegistrationPolicy::validName(str_repeat('A', 101)));
        self::assertTrue(AthleteRegistrationPolicy::validRelationship('mother'));
        self::assertTrue(AthleteRegistrationPolicy::validRelationship('legal_guardian'));
        self::assertFalse(AthleteRegistrationPolicy::validRelationship('unknown'));
    }

    public function testBirthAndEntryDatesAreConsistent(): void
    {
        self::assertTrue(AthleteRegistrationPolicy::validBirthAndEntry('2017-08-15', '2024-01-10'));
        self::assertFalse(AthleteRegistrationPolicy::validBirthAndEntry('2030-01-01', '2024-01-10'));
        self::assertFalse(AthleteRegistrationPolicy::validBirthAndEntry('2010-02-30', '2024-01-10'));
        self::assertFalse(AthleteRegistrationPolicy::validBirthAndEntry('2000-01-01', '2024-01-10'));
        self::assertFalse(AthleteRegistrationPolicy::validBirthAndEntry('2017-08-15', '2030-01-01'));
    }

    public function testContactPhoneIsOptionalAndBounded(): void
    {
        self::assertTrue(AthleteRegistrationPolicy::validPhone(''));
        self::assertTrue(AthleteRegistrationPolicy::validPhone('+503 2222-2222'));
        self::assertFalse(AthleteRegistrationPolicy::validPhone('número privado'));
    }
}
