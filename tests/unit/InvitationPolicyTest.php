<?php

declare(strict_types=1);

use App\Services\InvitationPolicy;
use PHPUnit\Framework\TestCase;

final class InvitationPolicyTest extends TestCase
{
    public function testOnlyOrdinaryRolesCanBeInvited(): void
    {
        foreach (['guardian', 'coach', 'media_contributor'] as $role) {
            self::assertTrue(InvitationPolicy::canInviteTo($role));
        }

        foreach (['institutional_admin', 'access_manager', 'technical_support', 'board_treasurer', 'equipment_manager', 'sports_director'] as $role) {
            self::assertFalse(InvitationPolicy::canInviteTo($role));
        }
    }

    public function testTokenValidationRequires64LowercaseHexCharacters(): void
    {
        self::assertTrue(InvitationPolicy::validToken(str_repeat('a', 64)));
        self::assertFalse(InvitationPolicy::validToken('invalid'));
        self::assertFalse(InvitationPolicy::validToken(str_repeat('A', 64)));
        self::assertFalse(InvitationPolicy::validToken(str_repeat('a', 63)));
    }

    public function testUsernameAcceptsOnlyShieldCompatibleCharacters(): void
    {
        self::assertTrue(InvitationPolicy::validUsername('padre.apellido'));
        self::assertFalse(InvitationPolicy::validUsername('padre_apellido'));
        self::assertFalse(InvitationPolicy::validUsername('ab'));
    }

    public function testPasswordRequirements(): void
    {
        self::assertTrue(InvitationPolicy::validPassword('Segura!2026Clave', 'Segura!2026Clave', 'usuario'));
        self::assertFalse(InvitationPolicy::validPassword('insegura', 'insegura', 'usuario'));
        self::assertFalse(InvitationPolicy::validPassword('Segura!2026Clave', 'distinta', 'usuario'));
        self::assertFalse(InvitationPolicy::validPassword('Usuario!2026Clave', 'Usuario!2026Clave', 'usuario'));
    }
}
