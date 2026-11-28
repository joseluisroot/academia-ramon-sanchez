<?php

declare(strict_types=1);

namespace Tests\Tickets;

use App\Services\Tickets\TicketAvailability;
use CodeIgniter\Test\CIUnitTestCase;

final class TicketAvailabilityTest extends CIUnitTestCase
{
    public function testFlagFailsClosed(): void
    {
        foreach ([false, null, '', 'false', 'yes', '1', 1, 'invalid'] as $value) {
            $this->assertFalse(TicketAvailability::parseFlag($value));
        }
        $this->assertTrue(TicketAvailability::parseFlag(true));
        $this->assertTrue(TicketAvailability::parseFlag(' true '));
    }

    public function testOnlyExplicitRoutesAndMethodsAreAllowed(): void
    {
        foreach ([['tickets','GET'], ['login','POST'], ['logout','GET'], ['auth/a/verify','POST']] as [$path,$method]) {
            $this->assertTrue(TicketAvailability::allowsDuringPrelaunch($path,$method));
        }
        foreach ([['tickets','POST'], ['tickets/admin','GET'], ['app','GET'], ['app/atletas','GET'], ['app/atletas','POST'], ['register','POST'], ['activar-cuenta/token','GET'], ['login/extra','GET'], ['logout','POST']] as [$path,$method]) {
            $this->assertFalse(TicketAvailability::allowsDuringPrelaunch($path,$method));
        }
    }
}
