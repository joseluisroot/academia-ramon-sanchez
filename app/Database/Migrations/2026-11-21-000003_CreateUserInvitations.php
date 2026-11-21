<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserInvitations extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 254],
            'role' => ['type' => 'VARCHAR', 'constraint' => 64],
            'token_hash' => ['type' => 'CHAR', 'constraint' => 64],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
            'invited_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME'],
            'expires_at' => ['type' => 'DATETIME'],
            'consumed_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('token_hash', 'uq_user_invitations_token_hash');
        $this->forge->addKey('email', false, false, 'idx_user_invitations_email');
        $this->forge->addKey(['status', 'expires_at'], false, false, 'idx_user_invitations_active');
        $this->forge->createTable('user_invitations', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('user_invitations', true);
    }
}
