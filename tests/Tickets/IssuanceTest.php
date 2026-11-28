<?php
declare(strict_types=1);
namespace Tests\Tickets;
use App\Services\Tickets\{TicketSecrets,IssuancePolicy,TicketAvailability};
use PHPUnit\Framework\TestCase;
final class IssuanceTest extends TestCase
{
    public function testSecretRoundTrip(): void {
        $box=new TicketSecrets(bin2hex(random_bytes(32)));
        $context=IssuancePolicy::context(1,2,3,4);
        $data=$box->generate($context);$plain=$box->decrypt($data['secret_envelope'],$context);
        self::assertMatchesRegularExpression('/^[a-f0-9]{64}$/D',$plain['token']);
        self::assertMatchesRegularExpression('/^[0-9A-HJKMNP-TV-Z]{16}$/D',$plain['manual']);
        self::assertSame(hash('sha256',$plain['token']),$data['token_hash']);
        self::assertSame(hash('sha256',$plain['manual']),$data['manual_hash']);
        self::assertNotSame($data['token_hash'],$box->generate($context)['token_hash']);
    }
    public function testWrongContextRejected(): void {
        $box=new TicketSecrets(bin2hex(random_bytes(32)));
        $data=$box->generate('a');
        $this->expectException(\RuntimeException::class);$box->decrypt($data['secret_envelope'],'b');
    }
    public function testMissingKeyRejected(): void {
        $this->expectException(\RuntimeException::class);new TicketSecrets('');
    }
    public function testQuota(): void {
        IssuancePolicy::assertAllowed('OPEN',100,60,40);
        $this->expectException(\InvalidArgumentException::class);IssuancePolicy::assertAllowed('OPEN',100,60,41);
    }
    public function testClosed(): void {
        $this->expectException(\InvalidArgumentException::class);IssuancePolicy::assertAllowed('CLOSED',100,0,1);
    }
    public function testRoutes(): void {
        self::assertTrue(TicketAvailability::allowsDuringPrelaunch('tickets/eventos/1/emision','POST'));
        self::assertTrue(TicketAvailability::allowsDuringPrelaunch('tickets/eventos/1/emision','GET'));
        self::assertFalse(TicketAvailability::allowsDuringPrelaunch('tickets/eventos/1/emision/extra','POST'));
    }
}
