<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Categoría histórica de cada marcación nueva y bitácora de transiciones de sesión.
 * Las marcaciones anteriores quedan sin categoría histórica: no se reconstruye
 * retrospectivamente a partir de la categoría actual del atleta.
 */
final class AddTrainingClosureAndCategorySnapshot extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('training_attendance', [
            'sports_category_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'athlete_id'],
        ]);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'session_id' => ['type' => 'INT', 'unsigned' => true],
            'from_status' => ['type' => 'VARCHAR', 'constraint' => 20],
            'to_status' => ['type' => 'VARCHAR', 'constraint' => 20],
            'changed_by' => ['type' => 'INT', 'unsigned' => true],
            'changed_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['session_id', 'changed_at'], false, false, 'idx_session_status_history');
        $this->forge->addForeignKey('session_id', 'training_sessions', 'id', 'CASCADE', 'RESTRICT', 'fk_training_status_session');
        $this->forge->createTable('training_session_status_history', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('training_session_status_history', true);
        $this->forge->dropColumn('training_attendance', 'sports_category_id');
    }
}
