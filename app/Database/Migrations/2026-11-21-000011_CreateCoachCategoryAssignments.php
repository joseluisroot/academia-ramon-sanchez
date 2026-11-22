<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Alcance por entrenador: una asignación única y revocable por categoría. */
final class CreateCoachCategoryAssignments extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'unsigned' => true],
            'sports_category_id' => ['type' => 'INT', 'unsigned' => true],
            'active' => ['type' => 'BOOLEAN', 'default' => true],
            'assigned_by' => ['type' => 'INT', 'unsigned' => true],
            'assigned_at' => ['type' => 'DATETIME'],
            'revoked_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'revoked_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['user_id', 'sports_category_id'], 'uq_coach_category_scope');
        $this->forge->addKey('sports_category_id');
        $this->forge->addForeignKey('sports_category_id', 'sports_categories', 'id', 'RESTRICT', 'RESTRICT', 'fk_coach_scope_category');
        $this->forge->createTable('coach_category_assignments', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('coach_category_assignments', true);
    }
}
