<?php
declare(strict_types=1);
namespace Tests\Tickets;
use App\Services\Tickets\PrintLayout;
use PHPUnit\Framework\TestCase;
final class PrintLayoutTest extends TestCase
{
    public function testSizes():void {
        self::assertSame([101.6,152.4,1],PrintLayout::page('single'));
        self::assertSame([304.8,457.2,9],PrintLayout::page('sheet'));
    }
    public function testPositions():void {
        self::assertSame([0,0.0,0.0],PrintLayout::position('sheet',0));
        self::assertSame([0,203.2,304.8],PrintLayout::position('sheet',8));
        self::assertSame([1,0.0,0.0],PrintLayout::position('sheet',9));
        self::assertSame([3,0.0,0.0],PrintLayout::position('single',3));
    }
    public function testPayload():void {
        self::assertSame('ARS1:'.str_repeat('a',64),PrintLayout::qrPayload(str_repeat('a',64)));
        $this->expectException(\InvalidArgumentException::class);PrintLayout::qrPayload('ARS-MUESTRA-NO-VALIDA');
    }
}
