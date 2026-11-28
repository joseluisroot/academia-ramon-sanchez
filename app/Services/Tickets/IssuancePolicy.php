<?php
declare(strict_types=1);
namespace App\Services\Tickets;
use InvalidArgumentException;
final class IssuancePolicy
{
    public const MAX_BATCH=500;
    public static function assertAllowed(string $status,int $quota,int $issued,int $quantity): void
    {
        if ($status!=='OPEN') throw new InvalidArgumentException('Solo se emiten entradas en eventos abiertos.');
        if ($quantity<1 || $quantity>self::MAX_BATCH || $quantity>$quota-$issued) throw new InvalidArgumentException('Cantidad inválida o cuota insuficiente (máximo 500 por lote).');
    }
    public static function context(int $event,int $type,int $batch,int $serial): string
    {
        return "ars-ticket:v1:$event:$type:$batch:$serial";
    }
}
