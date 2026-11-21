<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Dorsal vigente por categoría + historial auditable.
 * No impone una política automática de conservación al cambiar de categoría.
 */
final class CreateJerseyAssignments extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'athlete_id' => ['type' => 'INT', 'unsigned' => true],
            'sports_category_id' => ['type' => 'INT', 'unsigned' => true],
            'jersey_number' => ['type' => 'SMALLINT', 'unsigned' => true],
            'assigned_by' => ['type' => 'INT', 'unsigned' => true],
            'assigned_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['sports_category_id', 'jersey_number'], 'uq_active_jersey_category_number');
        $this->forge->addUniqueKey('athlete_id', 'uq_active_jersey_athlete');
        $this->forge->addForeignKey('athlete_id', 'athletes', 'id', 'CASCADE', 'RESTRICT', 'fk_current_jersey_athlete');
        $this->forge->addForeignKey('sports_category_id', 'sports_categories', 'id', 'RESTRICT', 'RESTRICT', 'fk_current_jersey_category');
        $this->forge->createTable('jersey_assignments', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'athlete_id' => ['type' => 'INT', 'unsigned' => true],
            'sports_category_id' => ['type' => 'INT', 'unsigned' => true],
            'jersey_number' => ['type' => 'SMALLINT', 'unsigned' => true],
            'event' => ['type' => 'VARCHAR', 'constraint' => 24],
            'actor_user_id' => ['type' => 'INT', 'unsigned' => true],
            'occurred_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['athlete_id', 'occurred_at'], false, false, 'idx_jersey_history_athlete');
        $this->forge->addForeignKey('athlete_id', 'athletes', 'id', 'CASCADE', 'RESTRICT', 'fk_jersey_history_athlete');
        $this->forge->addForeignKey('sports_category_id', 'sports_categories', 'id', 'RESTRICT', 'RESTRICT', 'fk_jersey_history_category');
        $this->forge->createTable('jersey_assignment_history', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('jersey_assignment_history', true);
        $this->forge->dropTable('jersey_assignments', true);
    }
}
