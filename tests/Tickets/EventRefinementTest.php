<?php
declare(strict_types=1);
namespace Tests\Tickets;
use App\Services\Tickets\EventPolicy;
use App\Services\Tickets\TicketAvailability;
use PHPUnit\Framework\TestCase;
final class EventRefinementTest extends TestCase
{
    public function testSlug(): void { self::assertSame('gala-ramon-sanchez-2026',EventPolicy::slug('Gala Ramón Sánchez 2026')); }
    public function testSummary(): void {
        self::assertSame(['assigned'=>180,'available'=>20,'cards'=>90,'cents'=>90000,'sheets'=>10],EventPolicy::summary(200,[['quota'=>90,'adult_qty'=>1,'student_qty'=>1,'price'=>'10.00']]));
    }
    public function testMutationRoutes(): void {
        self::assertTrue(TicketAvailability::allowsDuringPrelaunch('tickets/eventos/1/tipos/2','POST'));
        self::assertTrue(TicketAvailability::allowsDuringPrelaunch('tickets/eventos/1/tipos/2/eliminar','POST'));
        self::assertFalse(TicketAvailability::allowsDuringPrelaunch('tickets/eventos/1/tipos/2/eliminar','GET'));
        self::assertFalse(TicketAvailability::allowsDuringPrelaunch('tickets/eventos/1/tipos/2/extra','POST'));
    }
}
