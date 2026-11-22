# INC-004 — Entrenamientos y asistencia

**Rama:** `feature/inc-004-entrenamientos-asistencia`  
**Base:** `feature/inc-003-personas-atletas-familias` (PR #3 en borrador).  
**Estado:** primer bloque implementado, sin pruebas WAMP todavía.

## Alcance del primer corte

- Sesiones privadas `training_sessions`, categorías `training_session_categories` y marcaciones `training_attendance` mediante migración `2026-11-21-000010_CreateTrainingSessions.php`.
- Crear entrenamiento con fecha, hora inicial y final, lugar y una o varias categorías. Permite U7+U9 o U11+U13 compartidas.
- Listado de hasta 40 sesiones y pantalla de detalle.
- Registrar una asistencia por atleta/sesión: `present`, `absent`, `excused`. La misma marca puede cambiarse y el evento se audita; no se permiten duplicaciones por clave única.
- El listado de asistencia usa atletas **activos en su categoría actual**. No presupone que todos participaron, ni marca ausencias automáticamente.
- No registrar asistencias antes de la fecha de la sesión; no existe aún lógica de repetición automática, sesiones canceladas o cierre de asistencia.
- Se conserva `csrf_field()` en cada POST, validación de permisos en rutas, controlador y servicio y transacciones en operaciones.

## Permisos y seguridad

El permiso general `training.manage` ya está asignado también al entrenador. **En este primer corte NO se abre la consulta global de menores a entrenadores.** Los controladores y el servicio adicionalmente requieren el grupo `institutional_admin` o `sports_director`. La asignación específica de entrenadores por categoría será un segundo bloque, con filtrado de filas en servidor y pruebas de aislamiento. Padres, utilero y junta no pueden gestionar entrenamientos.

## Alcance por completar

- Asignaciones de categorías a entrenadores, cobertura temporal, suplencias y permisos por sesión.
- Modificar/cancelar/finalizar sesiones con trazabilidad y políticas de cierre/reapertura de asistencia.
- Asistencia consolidada por atleta y categoría, porcentajes y reportes para reconocimientos sin exponer expedientes a otros roles.
- Desvincular la visibilidad histórica de la categoría **actual** cuando el atleta cambie de categoría o estado. El historial se conserva, pero en este primer corte la pantalla de detalle lista la plantilla activa actual; se requiere instantánea por sesión para reportes históricos exactos.
- Programación opcional recurrente basada en calendario autorizado. Horario habitual miércoles y jueves 4:30–6:00 p. m., viernes cuando proceda; no se crea automáticamente.
- Protocolos deportivos de ausencias, justificaciones y datos sensibles (no registrar motivos médicos en texto libre).
- Auditoría de modificaciones/cierres y reportes para premiaciones anuales.

## Prueba local

```powershell
cd C:\wamp64\www\acedemia-ramon-sanchez
git fetch origin
git switch --track origin/feature/inc-004-entrenamientos-asistencia
php spark migrate --all
php spark migrate:status
php spark routes
```

Si la rama ya existe localmente, sustituir el `switch --track` por `git switch feature/inc-004-entrenamientos-asistencia` y `git pull --ff-only origin feature/inc-004-entrenamientos-asistencia`.

Abrir `http://localhost:8080/app/entrenamientos` con cuenta administradora. Crear una sesión de hoy con atletas **ficticios** activos en categorías seleccionadas; luego registrar presentes y ausentes desde el detalle.

**No fusionar a main ni habilitar producción hasta resolver permisos por categorías, históricos, documentos de seguridad y QA.**


## Segundo corte — asignación de categorías a entrenadores

**Código incorporado; pendiente de validación local.**

- Migración `2026-11-21-000011_CreateCoachCategoryAssignments.php`: asignaciones por `user_id` y `sports_category_id`, únicas y revocables, con usuario/fecha de asignación y revocación.
- Pantalla institucional `/app/entrenamientos/entrenadores` para administrar asignaciones. Solo el grupo `institutional_admin` con permiso explícito `training.assign` puede crear y revocar asignaciones; el rol de dirección deportiva conserva gestión de sesiones pero no asigna permisos por sí mismo.
- El usuario destino debe tener cuenta activa y rol `coach`; el catálogo solo acepta categorías activas.
- El entrenador con `training.manage` solo ve sesiones que incluyan al menos una categoría asignada. En una sesión multicategría solo aparecen las categorías y los atletas activos que tiene autorizados.
- No puede programar sesiones; sí marcar/corregir asistencia **únicamente en su categoría asignada y desde la fecha del entrenamiento**. El servicio vuelve a validar el alcance en el POST, de modo que ocultar el HTML no es el mecanismo de seguridad.
- La revocación retira la visibilidad en consultas posteriores; se conservan asignación y auditoría. Reasignar reactiva el vínculo, sin crear duplicados.
- `/app/atletas` continúa reservado a `athletes.manage` y no queda expuesto por el permiso de entrenamientos.
- No existe aún cobertura temporal, suplencias, asignación individual por atleta ni acceso al historial de un entrenador anterior. En sesiones compartidas, un entrenador ve horario y ubicación de la sesión completa, pero no la plantilla de categorías ajenas.

**Prueba local:** `git pull --ff-only origin feature/inc-004-entrenamientos-asistencia`; luego `php spark migrate --all`. Acceder con administrador institucional a `/app/entrenamientos/entrenadores`; asignar una categoría a una cuenta de entrenador activa y comprobar la consulta con esa cuenta.

**Advertencia:** no usar atletas reales para comprobar vulneraciones de acceso. Las pruebas negativas y la revisión de concurrencia de revocaciones siguen pendientes.
