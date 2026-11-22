<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

/**
 * Catálogo institucional inicial. Permisos por recurso/categoría
 * deben comprobarse, además, en los servicios del dominio.
 */
class AuthGroups extends ShieldAuthGroups
{
    public string $defaultGroup = 'guardian';

    public array $groups = [
        'institutional_admin' => [
            'title' => 'Administrador institucional',
            'description' => 'Dennis Sánchez y fundadores autorizados.',
        ],
        'access_manager' => [
            'title' => 'Responsable de accesos',
            'description' => 'Persona designada para administrar asignaciones.',
        ],
        'sports_director' => [
            'title' => 'Dirección deportiva',
            'description' => 'Supervisión y organización deportiva.',
        ],
        'coach' => [
            'title' => 'Entrenador',
            'description' => 'Operación deportiva según categorías asignadas.',
        ],
        'equipment_manager' => [
            'title' => 'Utilero',
            'description' => 'Inscripción y cobros ordinarios autorizados.',
        ],
        'board_treasurer' => [
            'title' => 'Tesorería de Junta',
            'description' => 'Administración del fondo común de Junta.',
        ],
        'board_member' => [
            'title' => 'Junta Directiva',
            'description' => 'Actividades y funciones de apoyo.',
        ],
        'media_contributor' => [
            'title' => 'Colaborador de contenido',
            'description' => 'Preparación de contenido audiovisual.',
        ],
        'guardian' => [
            'title' => 'Padre o responsable',
            'description' => 'Consulta privada de sus hijos.',
        ],
        'technical_support' => [
            'title' => 'Soporte técnico',
            'description' => 'Soporte limitado a tareas técnicas autorizadas.',
        ],
    ];

    public array $permissions = [
        'portal.access'        => 'Acceso al portal privado',
        'users.invite'         => 'Invitar usuarios autorizados',
        'users.assign'         => 'Asignar roles y permisos',
        'users.view'           => 'Consultar cuentas administrativas',
        'inquiries.view'       => 'Consultar solicitudes de información de familias',
        'inquiries.manage'     => 'Gestionar el seguimiento de consultas de familias',
        'athletes.view'        => 'Consultar expedientes autorizados',
        'athletes.manage'      => 'Gestionar atletas autorizados',
        'family.athletes.view'  => 'Consultar únicamente atletas familiares vinculados y verificados',
        'family.links.verify'   => 'Verificar vínculo de padre o responsable con cuenta y atleta',
        'consents.verify'       => 'Registrar aceptaciones documentadas revisadas institucionalmente',
        'training.manage'      => 'Gestionar entrenamientos y asistencia',
        'matches.manage'       => 'Gestionar partidos y convocatorias',
        'matches.verify'       => 'Verificar resultados oficiales',
        'billing.manage'       => 'Gestionar cobranza operativa',
        'billing.view-own'     => 'Consultar comprobantes familiares',
        'uniforms.manage'      => 'Gestionar pedidos de uniformes',
        'treasury.manage'      => 'Gestionar fondo común de Junta',
        'treasury.report'      => 'Revisar reportes de Junta',
        'treasury.view-family' => 'Consultar transparencia aprobada',
        'content.draft'        => 'Preparar borradores y multimedia',
        'content.publish'      => 'Aprobar y publicar contenido',
        'announcements.manage' => 'Gestionar avisos institucionales',
        'system.settings'      => 'Configurar políticas institucionales',
        'system.support'       => 'Herramientas de soporte técnico',
        'audit.view'           => 'Consultar bitácoras autorizadas',
    ];

    public array $matrix = [
        // La designación de un responsable único de asignación se
        // representa mediante un grupo separado ('access_manager').
        'institutional_admin' => [
            'portal.access', 'athletes.*', 'training.*', 'matches.*',
            'billing.*', 'uniforms.*', 'treasury.*', 'content.*',
            'announcements.*', 'system.settings', 'audit.view', 'users.view', 'inquiries.*', 'family.links.verify', 'consents.verify',
        ],
        'access_manager' => ['portal.access', 'users.invite', 'users.assign', 'users.view'],
        'sports_director' => ['portal.access', 'athletes.view', 'training.manage', 'matches.*'],
        'coach' => ['portal.access', 'athletes.view', 'training.manage', 'matches.manage', 'content.draft'],
        'equipment_manager' => ['portal.access', 'athletes.view', 'athletes.manage', 'billing.manage', 'uniforms.manage', 'content.draft', 'inquiries.view', 'inquiries.manage'],
        'board_treasurer' => ['portal.access', 'treasury.manage', 'treasury.report'],
        'board_member' => ['portal.access', 'treasury.report', 'content.draft'],
        'media_contributor' => ['portal.access', 'content.draft'],
        'guardian' => ['portal.access', 'family.athletes.view', 'billing.view-own', 'treasury.view-family'],
        'technical_support' => ['portal.access', 'system.support'],
    ];
}
