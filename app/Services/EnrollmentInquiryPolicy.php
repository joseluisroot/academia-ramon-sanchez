<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Restricciones para formularios públicos de personas adultas.
 * No se solicitan ni almacenan aquí datos identificativos de menores.
 */
final class EnrollmentInquiryPolicy
{
    public const CATEGORIES = ['U7', 'U9', 'U11', 'U13', 'U17', 'not_sure'];

    public static function validCategory(string $category): bool
    {
        return in_array($category, self::CATEGORIES, true);
    }

    public static function validName(string $name): bool
    {
        $size = mb_strlen(trim($name));

        return $size >= 3 && $size <= 120;
    }

    public static function validPhone(string $phone): bool
    {
        return $phone === '' || (mb_strlen($phone) <= 30 && preg_match('/^[0-9+() .-]{7,30}$/', $phone) === 1);
    }

    public static function validMessage(string $message): bool
    {
        return mb_strlen(trim($message)) <= 500;
    }
}
