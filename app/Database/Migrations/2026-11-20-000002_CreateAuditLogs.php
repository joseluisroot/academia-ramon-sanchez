<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Auditoría privada y minimizada. No insertar tokens,
 * contraseñas ni datos identificativos completos de menores.
 */
class CreateAuditLogs extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'actor_user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'action'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'subject_type' => ['type' => 'VARCHAR', 'constraint' => 80],
            'subject_id'   => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'reason'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'metadata_json' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME'],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('actor_user_id', false, false, 'idx_audit_actor');
        $this->forge->addKey('created_at', false, false, 'idx_audit_created_at');
        $this->forge->createTable('audit_logs', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('audit_logs', true);
    }
}
