<?php
declare(strict_types=1);
namespace App\Services\Tickets;
use InvalidArgumentException;
final class PrintLayout
{
    public const WIDTH=101.6;
    public const HEIGHT=152.4;
    public static function page(string $format): array
    {
        return match($format) {'single'=>[self::WIDTH,self::HEIGHT,1],'sheet'=>[304.8,457.2,9],default=>throw new InvalidArgumentException('Formato inválido.')};
    }
    public static function position(string $format,int $index): array
    {
        if ($index<0) throw new InvalidArgumentException('Índice inválido.');
        [,,$per]=self::page($format);$slot=$index%$per;
        return [intdiv($index,$per),($slot%3)*self::WIDTH,intdiv($slot,3)*self::HEIGHT];
    }
    public static function qrPayload(string $token): string
    {
        if (!preg_match('/^[a-f0-9]{64}$/D',$token)) throw new InvalidArgumentException('Token inválido.');
        return 'ARS1:'.$token;
    }
}
