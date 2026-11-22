<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Catálogo inicial confirmado durante el levantamiento.
 * La elegibilidad exacta por edad se definirá institucionalmente.
 */
final class SportsCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['U7', 'U9', 'U11', 'U13', 'U17'] as $code) {
            if ($this->db->table('sports_categories')->where('code', $code)->countAllResults() === 0) {
                $this->db->table('sports_categories')->insert([
                    'code' => $code,
                    'name' => 'Categoría ' . $code,
                    'age_min' => null,
                    'age_max' => null,
                    'active' => true,
                ]);
            }
        }
    }
}
