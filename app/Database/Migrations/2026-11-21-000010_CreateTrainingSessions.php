<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Sesiones compartidas por categorías y asistencia privada por atleta. */
final class CreateTrainingSessions extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'training_date' => ['type' => 'DATE'],
            'starts_at' => ['type' => 'TIME'],
            'ends_at' => ['type' => 'TIME'],
            'location_name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'scheduled'],
            'created_by' => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['training_date', 'starts_at'], false, false, 'idx_training_date');
        $this->forge->createTable('training_sessions', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'session_id' => ['type' => 'INT', 'unsigned' => true],
            'sports_category_id' => ['type' => 'INT', 'unsigned' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['session_id', 'sports_category_id'], 'uq_training_session_category');
        $this->forge->addForeignKey('session_id', 'training_sessions', 'id', 'CASCADE', 'RESTRICT', 'fk_training_category_session');
        $this->forge->addForeignKey('sports_category_id', 'sports_categories', 'id', 'RESTRICT', 'RESTRICT', 'fk_training_category_catalog');
        $this->forge->createTable('training_session_categories', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'session_id' => ['type' => 'INT', 'unsigned' => true],
            'athlete_id' => ['type' => 'INT', 'unsigned' => true],
            'attendance_status' => ['type' => 'VARCHAR', 'constraint' => 20],
            'recorded_by' => ['type' => 'INT', 'unsigned' => true],
            'recorded_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['session_id', 'athlete_id'], 'uq_training_attendance');
        $this->forge->addKey('athlete_id');
        $this->forge->addForeignKey('session_id', 'training_sessions', 'id', 'CASCADE', 'RESTRICT', 'fk_attendance_session');
        $this->forge->addForeignKey('athlete_id', 'athletes', 'id', 'RESTRICT', 'RESTRICT', 'fk_attendance_athlete');
        $this->forge->createTable('training_attendance', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('training_attendance', true);
        $this->forge->dropTable('training_session_categories', true);
        $this->forge->dropTable('training_sessions', true);
    }
}
