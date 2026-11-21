<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLogModel;
use InvalidArgumentException;

/**
 * Registrador de eventos auditables de la academia.
 * No registrar contraseñas, enlaces/tokens ni datos sensibles completos.
 */
final class AuditLogger
{
    public function __construct(private ?AuditLogModel $model = null)
    {
        $this->model ??= new AuditLogModel();
    }

    /**
     * @param array<string, scalar|null> $metadata Solo claves aprobadas.
     */
    public function record(
        ?int $actorUserId,
        string $action,
        string $subjectType,
        ?string $subjectId = null,
        ?string $reason = null,
        array $metadata = [],
    ): void {
        $allowed = ['role', 'target_role', 'result', 'source', 'scope'];

        foreach (array_keys($metadata) as $key) {
            if (! in_array($key, $allowed, true)) {
                throw new InvalidArgumentException('Clave de auditoría no permitida.');
            }
        }

        $this->model->insert([
            'actor_user_id' => $actorUserId,
            'action'        => $action,
            'subject_type'  => $subjectType,
            'subject_id'    => $subjectId,
            'reason'        => $reason,
            'metadata_json' => json_encode($metadata, JSON_THROW_ON_ERROR),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }
}
