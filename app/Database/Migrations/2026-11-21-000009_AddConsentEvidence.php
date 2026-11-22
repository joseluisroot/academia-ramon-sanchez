<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Traceable evidence references for signed consent declarations.
 * No uploaded files, document contents or sensitive identity data stored here.
 */
final class AddConsentEvidence extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('athlete_consent_register', [
            'evidence_reference' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'signed_on' => ['type' => 'DATE', 'null' => true],
            'expires_on' => ['type' => 'DATE', 'null' => true],
            'evidence_verified_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'evidence_verified_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('athlete_consent_register', [
            'evidence_reference', 'signed_on', 'expires_on',
            'evidence_verified_by', 'evidence_verified_at',
        ]);
    }
}
