<?php

declare(strict_types=1);

use App\Services\RoleAssignmentPolicy;
use PHPUnit\Framework\TestCase;

final class RoleAssignmentPolicyTest extends TestCase
{
    public function testOnlyOrdinaryRolesCanBeAssigned(): void
    {
        self::assertTrue(RoleAssignmentPolicy::canAssign('guardian'));
        self::assertTrue(RoleAssignmentPolicy::canAssign('coach'));
        self::assertTrue(RoleAssignmentPolicy::canAssign('media_contributor'));

        foreach (['institutional_admin', 'access_manager', 'technical_support', 'board_treasurer', 'equipment_manager', 'sports_director'] as $group) {
            self::assertFalse(RoleAssignmentPolicy::canAssign($group));
        }
    }

    public function testPrivilegedAccountsAreProtected(): void
    {
        self::assertTrue(RoleAssignmentPolicy::isProtectedAccount(['guardian', 'institutional_admin']));
        self::assertTrue(RoleAssignmentPolicy::isProtectedAccount(['access_manager']));
        self::assertTrue(RoleAssignmentPolicy::isProtectedAccount(['technical_support']));
        self::assertFalse(RoleAssignmentPolicy::isProtectedAccount(['guardian', 'coach']));
    }
}
