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


## Tercer bloque — cierre de sesiones y reporte conservador de asistencia

**Migración `2026-11-22-000012_AddTrainingClosureAndCategorySnapshot.php`:**
- Crea `training_session_status_history` para guardar transiciones con usuario y fecha.
- Añade `training_attendance.sports_category_id` nullable, que registra **la categoría al momento de crear una marca nueva**.
- Las marcas existentes quedan `NULL`, porque inferir su categoría a partir del expediente actual podría falsear el histórico después de un cambio de categoría. No se aplica backfill ficticio.

**Ciclo de la sesión:** `scheduled → completed` (finalizada) o `scheduled → cancelled` (cancelada). Son transiciones definitivas en esta versión; no hay reapertura ni eliminación de registros. Solo administración o dirección deportiva pueden cambiar el estado.

**Reglas:**
- Finalizar requiere fecha actual o pasada y al menos una marca de asistencia. No marca automáticamente como ausentes a quienes siguen sin registrar; en sesiones con marcas parciales, se muestra una advertencia y las faltantes quedarán sin marca.
- Cancelar solo procede si todavía no hay ninguna asistencia registrada. Finalizar o cancelar bloquea correcciones posteriores y conserva historial y auditoría.
- El entrenador continúa pudiendo marcar su categoría mientras la sesión esté programada, pero no puede finalizar/cancelar.
- El listado de sesiones distingue Programado, Finalizado y Cancelado.

**Reporte:** `GET /app/entrenamientos/reporte` muestra para atletas con marcas en sesiones finalizadas el número de presentes, ausentes, justificados y total marcado, separados por categoría histórica. El entrenador solo ve las categorías que tiene asignadas en el momento de consultar. La dirección deportiva y los administradores ven las marcas categorizadas de todas las categorías.

**Limitaciones explícitas:**
- No se presentan porcentajes ni clasificación anual, porque la ausencia de marca no demuestra ausencia real.
- Las marcas creadas antes de la migración 000012 siguen conservadas en las sesiones, pero no aparecen en el reporte por categoría hasta contar con una reconstrucción auditada del histórico.
- La categoría queda fijada al registrar por primera vez la asistencia. La lista disponible en una sesión todavía depende de la categoría actual del atleta; se requiere diseñar una plantilla histórica por sesión para cambios de categoría, inactividad y reingresos.
- No se permite corregir una sesión cerrada ni existe cierre automático; la reapertura formal y aprobación de correcciones requieren procedimiento específico.

**Para probar en WAMP:** actualizar rama, `php spark migrate --all`, crear una sesión ficticia del día, marcar presente/ausente/justificado, finalizar como administrador o director y acceder al reporte. Para cancelación, crear otra sesión sin asistencias. No finalizar sesiones reales de prueba: el cierre no se puede revertir aún.


## Cuarto bloque — plantilla histórica al cierre (22/11/2026)

- Migración `2026-11-22-000013_CreateTrainingSessionRoster.php`: `training_session_roster`, un atleta por entrenamiento con categoría capturada y fecha de congelamiento.
- Al **finalizar** un entrenamiento se captura dentro de la misma transacción la plantilla activa de las categorías de la sesión, y se incorporan los atletas que ya tenían marcación conservando la categoría en la que se registró su asistencia.
- En detalle de sesiones **finalizadas después de instalar 000013**, se muestra esa plantilla, incluso si posteriormente los atletas cambian de categoría o quedan inactivos. La consulta sigue filtrada en servidor por categorías asignadas al entrenador.
- Las sesiones programadas continúan mostrando atletas activos de categorías actuales; las canceladas no muestran plantilla.
- No se altera el estado de asistencia de quienes no fueron marcados. No hay denominador oficial para porcentajes.
- **Limitación importante:** la plantilla representa los atletas conocidos *en el momento de cerrar*, no necesariamente los inscritos o convocados en el día del entrenamiento. Las sesiones finalizadas antes de esta migración **no tienen** plantilla histórica retroactiva: el detalle de plantilla puede estar vacío. No inventar un histórico mediante inferencia.
- **Pendiente antes de cerrar INC-004:** reglas de captura en fecha efectiva de la sesión, altas/bajas tardías, política de reapertura, pruebas de datos antiguos, concurrencia y seguridad. Los reportes actualmente cuentan solo marcaciones verificadas de sesiones finalizadas.

**Validación sugerida:** crear sesión ficticia de hoy, marcar asistencia y finalizar; comprobar plantilla. Posteriormente cambiar categoría o estado de un atleta ficticio y verificar que el detalle cerrado muestra la categoría histórica. No hacer pruebas irreversibles con atletas reales.


## Incidencia INC-004-HIS-001 — asistencia y navegación del portal (22/11/2026)

El usuario mostró captura de una sesión U9 finalizada con una marca **Presente: 1**, y la fila del atleta confirma **Presente**. La escritura de asistencia se evidencia en pantalla y en la base de datos, pero el encabezado **«Categoría actual»** señala que la pantalla capturada no corresponde a la última plantilla histórica ya subida. También se observó cierre almacenado el 23/11 a las 02:34, compatible con el día 22/11 a las 20:34 en El Salvador si el sello técnico se almacena en UTC.

**Corrección de compatibilidad:** para sesiones cerradas antes de activar `training_session_roster`, el detalle recupera marcaciones existentes por categoría histórica cuando se conoce, sin inventar roster ni ausencias. Si la categoría antigua es nula, solo en sesiones de **una única categoría** se permite visualizar la marca con la etiqueta «sin verificar» sin modificar la base de datos. Se indica al usuario el estado de registro histórico. Se aisló además el resumen agregado de asistencias para que un entrenador de una categoría no vea totales de otra en sesiones mixtas.

**Zona horaria:** `TrainingClock` usa el día deportivo `America/El_Salvador` para decidir fechas y el momento actual UTC para nuevos sellos técnicos, mostrando cambios de estado en hora local. Las marcas legadas con zona de origen incierta deben verificarse antes de conversiones masivas; no se cambia el valor persistido.

**Navegación:** se entrega un primer panel `/app` con secciones reales por perfil, más enlaces persistentes filtrados por permiso; el dashboard completo se registró en UX-PL-007 y roadmap.

**Validación:** actualizar rama, limpiar caché de navegación si procede, recargar la sesión finalizada y verificar encabezado/estado. No volver a registrar la asistencia existente ni editar filas directamente para «corregir» la vista.
