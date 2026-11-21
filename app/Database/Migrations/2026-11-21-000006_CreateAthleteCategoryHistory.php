<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Registro inmutable de las categorías, inicial y futuras. */
final class CreateAthleteCategoryHistory extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'athlete_id' => ['type' => 'INT', 'unsigned' => true],
            'sports_category_id' => ['type' => 'INT', 'unsigned' => true],
            'effective_on' => ['type' => 'DATE'],
            'reason' => ['type' => 'VARCHAR', 'constraint' => 30],
            'recorded_by' => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['athlete_id', 'effective_on'], false, false, 'idx_athlete_category_timeline');
        $this->forge->addForeignKey('athlete_id', 'athletes', 'id', 'CASCADE', 'RESTRICT', 'fk_category_history_athlete');
        $this->forge->addForeignKey('sports_category_id', 'sports_categories', 'id', 'RESTRICT', 'RESTRICT', 'fk_category_history_category');
        $this->forge->createTable('athlete_category_history', true);

        // Los atletas creados antes de esta migración también necesitan su categoría inicial.
        $athletes = $this->db->table('athletes')
            ->select('id, sports_category_id, joined_on, created_by, created_at')
            ->get()->getResultArray();

        foreach ($athletes as $athlete) {
            $this->db->table('athlete_category_history')->insert([
                'athlete_id' => (int) $athlete['id'],
                'sports_category_id' => (int) $athlete['sports_category_id'],
                'effective_on' => (string) $athlete['joined_on'],
                'reason' => 'initial',
                'recorded_by' => (int) $athlete['created_by'],
                'created_at' => (string) ($athlete['created_at'] ?? date('Y-m-d H:i:s')),
            ]);
        }
    }

    public function down(): void
    {
        $this->forge->dropTable('athlete_category_history', true);
    }
}
