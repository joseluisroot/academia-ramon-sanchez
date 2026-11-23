<?php

declare(strict_types=1);

namespace App\Services;

use CodeIgniter\Shield\Entities\User;

/**
 * Non-sensitive, permission-scoped operational summaries.
 * Do not reuse these counts to authorize access: controllers enforce scopes.
 */
final class PortalDashboardService
{
    /** @return array{role:string,subtitle:string,metrics:list<array{label:string,value:string,note:string}>,next:array{label:string,url:string}|null} */
    public static function build(User $actor): array
    {
        $db = db_connect();
        $supervisor = CoachCategoryScopeService::isSupervisor($actor);
        $coach = ! $supervisor && CoachCategoryScopeService::isCoach($actor);

        if ($supervisor || $coach) {
            $sessions = $db->table('training_sessions s');
            if ($coach) {
                $ids = CoachCategoryScopeService::allowedCategoryIds($actor);
                if ($ids === []) {
                    $sessions->where('s.id', 0);
                } else {
                    $sessions->join('training_session_categories sc', 'sc.session_id = s.id')
                        ->whereIn('sc.sports_category_id', $ids);
                }
            }
            $sessions->where('s.status', 'scheduled');
            $scheduled = $coach
                ? (int) $sessions->select('COUNT(DISTINCT s.id) AS total', false)->get()->getRow('total')
                : (int) $sessions->countAllResults();

            $athletes = $db->table('athletes a')->where('a.status', 'active');
            if ($coach) {
                $ids = CoachCategoryScopeService::allowedCategoryIds($actor);
                $ids === [] ? $athletes->where('a.id', 0) : $athletes->whereIn('a.sports_category_id', $ids);
            }
            $activeAthletes = (int) $athletes->countAllResults();

            $categories = $coach
                ? count(CoachCategoryScopeService::allowedCategoryIds($actor))
                : (int) $db->table('sports_categories')->where('active', 1)->countAllResults();

            return [
                'role' => $coach ? 'Panel del entrenador' : ($actor->inGroup('institutional_admin') ? 'Dirección institucional' : 'Dirección deportiva'),
                'subtitle' => $coach ? 'Seguimiento de tus categorías y entrenamientos autorizados.' : 'Panorama operativo de la academia y su actividad deportiva.',
                'metrics' => [
                    ['label' => 'Atletas activos', 'value' => (string) $activeAthletes, 'note' => $coach ? 'En tus categorías' : 'En la academia'],
                    ['label' => 'Categorías', 'value' => (string) $categories, 'note' => $coach ? 'Asignadas a tu cuenta' : 'Activas'],
                    ['label' => 'Sesiones programadas', 'value' => (string) $scheduled, 'note' => $coach ? 'De tus categorías' : 'Pendientes de finalizar'],
                ],
                'next' => ['label' => 'Ir a entrenamientos', 'url' => 'app/entrenamientos'],
            ];
        }

        if ($actor->can('athletes.manage')) {
            return [
                'role' => 'Gestión administrativa',
                'subtitle' => 'Control del registro deportivo y atención a las familias.',
                'metrics' => [
                    ['label' => 'Atletas activos', 'value' => (string) $db->table('athletes')->where('status', 'active')->countAllResults(), 'note' => 'Expedientes vigentes'],
                    ['label' => 'Categorías', 'value' => (string) $db->table('sports_categories')->where('active', 1)->countAllResults(), 'note' => 'Catálogo deportivo'],
                ],
                'next' => ['label' => 'Administrar atletas', 'url' => 'app/atletas'],
            ];
        }

        if ($actor->can('family.athletes.view')) {
            // No query on unverified guardianships. Private family details belong
            // to the already authorization-filtered /app/mis-atletas controller.
            return [
                'role' => 'Portal familiar',
                'subtitle' => 'Un espacio privado para acompañar la actividad deportiva de tus hijos.',
                'metrics' => [],
                'next' => ['label' => 'Ver mis atletas', 'url' => 'app/mis-atletas'],
            ];
        }

        return [
            'role' => $actor->inGroup('board_treasurer') || $actor->inGroup('board_member')
                ? 'Panel de Junta Directiva' : 'Panel de trabajo',
            'subtitle' => 'Accede a las herramientas habilitadas para tus funciones institucionales.',
            'metrics' => [],
            'next' => null,
        ];
    }
}
