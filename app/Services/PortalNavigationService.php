<?php

declare(strict_types=1);

namespace App\Services;

use CodeIgniter\Shield\Entities\User;

/**
 * Navigation is only a convenience; all routes and domain services MUST
 * enforce their own authorization and per-category/per-family boundaries.
 */
final class PortalNavigationService
{
    /** @return array<int, array{title:string, description:string, items:array<int,array{label:string,url:string}>}> */
    public static function sections(?User $user): array
    {
        if ($user === null || ! $user->can('portal.access')) {
            return [];
        }

        $sections = [];

        if (CoachCategoryScopeService::isSupervisor($user) || CoachCategoryScopeService::isCoach($user)) {
            $items = [
                ['label' => 'Entrenamientos y asistencia', 'url' => 'app/entrenamientos'],
                ['label' => 'Reporte de asistencia', 'url' => 'app/entrenamientos/reporte'],
            ];
            if ($user->can('training.assign') && $user->inGroup('institutional_admin')) {
                $items[] = ['label' => 'Asignar categorías a entrenadores', 'url' => 'app/entrenamientos/entrenadores'];
            }
            $sections[] = [
                'title' => 'Gestión deportiva',
                'description' => CoachCategoryScopeService::isCoach($user) && ! CoachCategoryScopeService::isSupervisor($user)
                    ? 'Tus entrenamientos y atletas de categorías asignadas.'
                    : 'Programación, seguimiento y control institucional.',
                'items' => $items,
            ];
        }

        if ($user->can('athletes.manage')) {
            $items = [['label' => 'Registro de atletas', 'url' => 'app/atletas']];
            if ($user->can('family.links.verify')) {
                $items[] = ['label' => 'Verificar responsables', 'url' => 'app/familias/verificar'];
            }
            $sections[] = [
                'title' => 'Expedientes y familias',
                'description' => 'Inscripciones, categorías y relaciones familiares autorizadas.',
                'items' => $items,
            ];
        }

        if ($user->can('family.athletes.view')) {
            $sections[] = [
                'title' => 'Mi familia',
                'description' => 'Consulta privada de deportistas con vínculos verificados.',
                'items' => [['label' => 'Mis atletas', 'url' => 'app/mis-atletas']],
            ];
        }

        if ($user->can('inquiries.view')) {
            $sections[] = [
                'title' => 'Atención a familias',
                'description' => 'Solicitudes de información y seguimiento de contacto.',
                'items' => [['label' => 'Consultas recibidas', 'url' => 'app/consultas']],
            ];
        }

        $access = [];
        if ($user->can('users.assign')) {
            $access[] = ['label' => 'Administrar accesos', 'url' => 'app/accesos'];
        }
        if ($user->can('users.invite')) {
            $access[] = ['label' => 'Invitar usuarios', 'url' => 'app/invitaciones'];
        }
        if ($access !== []) {
            $sections[] = [
                'title' => 'Cuentas y permisos',
                'description' => 'Acciones administrativas autorizadas.',
                'items' => $access,
            ];
        }

        return $sections;
    }
}
