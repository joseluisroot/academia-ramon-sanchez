<?php
declare(strict_types=1);
namespace App\Services\Tickets;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

final class EventPolicy
{
    public static function utc(string $value): string
    {
        $dt = DateTimeImmutable::createFromFormat('!Y-m-d\\TH:i', $value, new DateTimeZone('America/El_Salvador'));
        if (!$dt || $dt->format('Y-m-d\\TH:i') !== $value) {
            throw new InvalidArgumentException('Fecha u hora inválida.');
        }
        return $dt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');
    }
    public static function local(string $value): string
    {
        return (new DateTimeImmutable($value, new DateTimeZone('UTC')))->setTimezone(new DateTimeZone('America/El_Salvador'))->format('Y-m-d\\TH:i');
    }
    public static function transition(string $from, string $to): bool
    {
        return in_array($to, ['DRAFT'=>['OPEN','CANCELLED'],'OPEN'=>['CLOSED','CANCELLED']][$from] ?? [], true);
    }
    public static function seats(int $quota, int $adults, int $students): int
    {
        if ($quota<1 || $quota>100000 || $adults<0 || $students<0 || $adults+$students<1 || $adults+$students>100) {
            throw new InvalidArgumentException('Cantidad o beneficio inválido.');
        }
        return $quota*($adults+$students);
    }
}
