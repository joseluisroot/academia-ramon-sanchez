<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Lifecycle trail and documentation tracking only.
 * Consent records NEVER authorize publication of minors' materials by themselves.
 */
final class CreateAthleteLifecycleAndConsentRegistry extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'athlete_id' => ['type' => 'INT', 'unsigned' => true],
            'from_status' => ['type' => 'VARCHAR', 'constraint' => 20],
            'to_status' => ['type' => 'VARCHAR', 'constraint' => 20],
            'reason_code' => ['type' => 'VARCHAR', 'constraint' => 30],
            'changed_by' => ['type' => 'INT', 'unsigned' => true],
            'changed_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['athlete_id', 'changed_at'], false, false, 'idx_athlete_status_timeline');
        $this->forge->addForeignKey('athlete_id', 'athletes', 'id', 'CASCADE', 'RESTRICT', 'fk_status_history_athlete');
        $this->forge->createTable('athlete_status_history', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'athlete_id' => ['type' => 'INT', 'unsigned' => true],
            'guardian_relation_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'consent_type' => ['type' => 'VARCHAR', 'constraint' => 28],
            'record_state' => ['type' => 'VARCHAR', 'constraint' => 20],
            'document_version' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'recorded_by' => ['type' => 'INT', 'unsigned' => true],
            'recorded_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['athlete_id', 'consent_type', 'recorded_at'], false, false, 'idx_consent_timeline');
        $this->forge->addForeignKey('athlete_id', 'athletes', 'id', 'CASCADE', 'RESTRICT', 'fk_consent_athlete');
        $this->forge->addForeignKey('guardian_relation_id', 'athlete_guardians', 'id', 'RESTRICT', 'RESTRICT', 'fk_consent_guardian');
        $this->forge->createTable('athlete_consent_register', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('athlete_consent_register', true);
        $this->forge->dropTable('athlete_status_history', true);
    }
}
