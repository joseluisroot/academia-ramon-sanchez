# INC-004 · Guía de validación crítica antes de cerrar
Fecha de preparación: 22/11/2026. **Estado: pendiente de ejecución en WAMP.**

## Alcance
La comprobación manual previa de un entrenador con categorías asignadas, el registro de una asistencia y el cierre de una sesión no prueba que las rutas directas, cambios de categoría o sesiones compartidas estén protegidos. Esta guía complementa `pruebas-pendientes.md`.

## Preparación
1. Respaldar la base de datos local antes de las pruebas con estados irreversibles.
2. Actualizar `feature/inc-004-entrenamientos-asistencia`.
3. Ejecutar `php spark migrate --all`, `php spark migrate:status`.
4. Crear **usuarios y atletas ficticios**, nunca reutilizar expedientes reales para pruebas destructivas: administrador institucional, entrenador A (solo U7), entrenador B (solo U9) y persona sin permiso deportivo.
5. Crear una sesión **ficticia** del día actual con U7 + U9; otras sesiones independientes para cierre y cancelación. El cierre no se puede revertir desde la interfaz.
6. Mantener las herramientas de desarrollo abiertas (Network y Console) para observar respuestas HTTP.

## Casos prioritarios

| ID | Prueba | Resultado esperado |
|---|---|---|
| SEC-01 | Entrenador A abre sesión U7+U9 | Ve solo atletas U7 y categorías autorizadas |
| SEC-02 | Entrenador A envía POST con `athlete_id` de U9 y token CSRF legítimo | Rechazado; sin cambio en base de datos |
| SEC-03 | Entrenador A abre `/app/atletas` | 403 o rechazo institucional equivalente |
| SEC-04 | Entrenador A intenta POST a `/app/entrenamientos/{id}/estado` | 403, no cambia estado |
| SEC-05 | Quitar asignación U7 al entrenador A y recargar sesión y reporte | Pierde acceso; no conserva datos en navegación |
| SEC-06 | Crear y marcar un atleta ficticio U7; moverlo a U9 **antes** de cerrar e intentar corregir su marca como entrenador A/B | Modificación rechazada por categoría histórica distinta; supervisor también debe solicitar revisión manual en esta versión |
| SEC-07 | Cerrar sesión U7+U9 desde administrador | Histórico conserva marcas; roster congelado al cierre; sin ausencias inventadas |
| SEC-08 | Entrenador A consulta sesión U7+U9 finalizada y su reporte | No ve marcas ni totales U9 |
| SEC-09 | Abrir sesión programada o cancelada | No produce aviso PHP de `rosterCount` no definido |
| SEC-10 | Cerrar sesión del futuro o sin ninguna marca | Rechazado; no modifica historial |
| SEC-11 | Cancelar sesión sin marcas; tratar de marcarla posteriormente | Cancelación correcta, POST de asistencia rechazado |
| SEC-12 | Repetir cierre o cancelación sobre sesión cerrada | Rechazado, sin duplicar historial |
| SEC-13 | Sesión antigua finalizada sin plantilla capturada | Vista de marcas existentes sin fabricar roster; marcas sin categoría no visibles para entrenador |
| SEC-14 | Omitir o manipular token CSRF en POST | Rechazado por filtro global; sin cambios |
| SEC-15 | Dos cierres simultáneos de una misma sesión ficticia | Una sola transición e historial; la segunda falla sin duplicar filas |

## Lecturas SQL auxiliares (solo consultas)
Reemplazar `42` por el ID **ficticio** de prueba, no por una sesión real.

```sql
SELECT id, training_date, status FROM training_sessions WHERE id = 42;

SELECT ta.session_id, ta.athlete_id, ta.sports_category_id,
       ta.attendance_status, ta.recorded_at
FROM training_attendance ta
WHERE ta.session_id = 42
ORDER BY ta.athlete_id;

SELECT tr.session_id, tr.athlete_id, tr.sports_category_id, tr.captured_at
FROM training_session_roster tr
WHERE tr.session_id = 42
ORDER BY tr.athlete_id;

SELECT session_id, from_status, to_status, changed_by, changed_at
FROM training_session_status_history
WHERE session_id = 42
ORDER BY id;
```

## Consideraciones y límites
- Las sesiones cerradas antes de `000013` no tienen plantilla histórica reconstruible; evitar migraciones que inventen miembros de una sesión anterior.
- En sesiones de una categoría con marcaciones anteriores sin categoría histórica, **solo el supervisor** puede visualizar las marcas como «sin verificar». La categoría no se asigna silenciosamente.
- Un cambio de categoría después de marcar impide corregir esa marca en esta versión; requiere procedimiento administrativo trazable, aún no implementado.
- El roster congelado refleja el momento del cierre, **no necesariamente los inscritos del día de entrenamiento**; no debe utilizarse como denominador oficial de premios.
- Todos los resultados son pendientes hasta que se ejecuten en WAMP y se documenten evidencias. El PR #4 continúa en borrador.
