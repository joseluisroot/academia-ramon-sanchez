<?php
declare(strict_types=1);
namespace Tests\Tickets;
use App\Services\Tickets\EventPolicy;
use PHPUnit\Framework\TestCase;
final class EventPolicyTest extends TestCase
{
    public function testTime(): void {
        self::assertSame('2026-12-18 23:00:00',EventPolicy::utc('2026-12-18T17:00'));
        self::assertSame('2026-12-18T17:00',EventPolicy::local('2026-12-18 23:00:00'));
    }
    public function testTransitions(): void {
        self::assertTrue(EventPolicy::transition('DRAFT','OPEN'));
        self::assertTrue(EventPolicy::transition('OPEN','CLOSED'));
        self::assertFalse(EventPolicy::transition('CLOSED','OPEN'));
    }
    public function testCapacity(): void { self::assertSame(200,EventPolicy::seats(100,1,1)); }
    public function testInvalidCapacity(): void { $this->expectException(\InvalidArgumentException::class); EventPolicy::seats(100,0,0); }
}
