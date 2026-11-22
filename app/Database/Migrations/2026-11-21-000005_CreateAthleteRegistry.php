<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Registro privado de atletas menores y sus responsables.
 * No crea cuentas Shield, cobros, fotografías ni perfiles públicos.
 */
final class CreateAthleteRegistry extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'code' => ['type' => 'VARCHAR', 'constraint' => 12],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'age_min' => ['type' => 'TINYINT', 'unsigned' => true, 'null' => true],
            'age_max' => ['type' => 'TINYINT', 'unsigned' => true, 'null' => true],
            'active' => ['type' => 'BOOLEAN', 'default' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('code', 'uq_sports_categories_code');
        $this->forge->createTable('sports_categories', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'first_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'last_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'birth_date' => ['type' => 'DATE'],
            'joined_on' => ['type' => 'DATE'],
            'sports_category_id' => ['type' => 'INT', 'unsigned' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'],
            'created_by' => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('sports_category_id');
        $this->forge->addForeignKey('sports_category_id', 'sports_categories', 'id', 'RESTRICT', 'RESTRICT', 'fk_athlete_category');
        $this->forge->createTable('athletes', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'athlete_id' => ['type' => 'INT', 'unsigned' => true],
            'person_id' => ['type' => 'INT', 'unsigned' => true],
            'relationship' => ['type' => 'VARCHAR', 'constraint' => 24],
            'is_primary' => ['type' => 'BOOLEAN', 'default' => false],
            'verified_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'verified_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['athlete_id', 'person_id'], 'uq_athlete_guardian');
        $this->forge->addKey('person_id');
        $this->forge->addForeignKey('athlete_id', 'athletes', 'id', 'CASCADE', 'RESTRICT', 'fk_guardian_athlete');
        $this->forge->addForeignKey('person_id', 'persons', 'id', 'RESTRICT', 'RESTRICT', 'fk_guardian_person');
        $this->forge->createTable('athlete_guardians', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('athlete_guardians', true);
        $this->forge->dropTable('athletes', true);
        $this->forge->dropTable('sports_categories', true);
    }
}
