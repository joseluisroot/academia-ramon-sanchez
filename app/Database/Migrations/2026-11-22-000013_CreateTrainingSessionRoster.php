<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Snapshot of roster at training closure. Historical snapshots are not
 * fabricated for previously closed sessions.
 */
final class CreateTrainingSessionRoster extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'session_id' => ['type' => 'INT', 'unsigned' => true],
            'athlete_id' => ['type' => 'INT', 'unsigned' => true],
            'sports_category_id' => ['type' => 'INT', 'unsigned' => true],
            'captured_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['session_id', 'athlete_id'], 'uq_training_roster_session_athlete');
        $this->forge->addKey(['session_id', 'sports_category_id'], false, false, 'idx_training_roster_category');
        $this->forge->addForeignKey('session_id', 'training_sessions', 'id', 'CASCADE', 'RESTRICT', 'fk_roster_session');
        $this->forge->addForeignKey('athlete_id', 'athletes', 'id', 'RESTRICT', 'RESTRICT', 'fk_roster_athlete');
        $this->forge->addForeignKey('sports_category_id', 'sports_categories', 'id', 'RESTRICT', 'RESTRICT', 'fk_roster_category');
        $this->forge->createTable('training_session_roster', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('training_session_roster', true);
    }
}
