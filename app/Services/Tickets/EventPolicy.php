<?php
declare(strict_types=1);
namespace App\Services\Tickets;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

final class EventPolicy
{
    public static function slug(string $name): string
    {
        $ascii = transliterator_transliterate('Any-Latin; Latin-ASCII', $name);
        $slug = trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower($ascii ?: $name)), '-');
        return rtrim(substr($slug, 0, 100), '-');
    }

    public static function summary(int $capacity, array $types): array
    {
        $seats = $cards = $cents = 0;
        foreach ($types as $type) {
            $quota = (int) $type['quota'];
            $seats += self::seats($quota, (int) $type['adult_qty'], (int) $type['student_qty']);
            $cards += $quota;
            $parts = explode('.', (string) $type['price'], 2);
            $cents += $quota * ((int) $parts[0] * 100 + (int) str_pad($parts[1] ?? '', 2, '0'));
        }
        return ['assigned'=>$seats,'available'=>$capacity-$seats,'cards'=>$cards,'cents'=>$cents,'sheets'=>intdiv($cards+8,9)];
    }

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
