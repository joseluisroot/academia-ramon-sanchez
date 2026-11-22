# INC-004 — Pruebas pendientes

La creación de código en GitHub **no equivale a prueba en WAMP**.

- [ ] **ENT-01** Ejecutar migración 000010 sin errores ni pérdida de atletas existentes.
- [ ] **ENT-02** Crear sesión con U7 y U9 y comprobar dos filas de categorías sin duplicación.
- [ ] **ENT-03** Crear sesión con U11 y U13, sin mezclar atletas ajenos.
- [ ] **ENT-04** Validar fecha válida, horario fin posterior al inicio y ubicación.
- [ ] **ENT-05** Rechazar fecha anterior a hoy, categoría inactiva, inexistente o vacía.
- [ ] **ENT-06** Detalle de sesión lista únicamente atletas activos con categoría seleccionada.
- [ ] **ENT-07** Registrar presente, ausente y justificado en sesión de hoy.
- [ ] **ENT-08** Corregir una marca y comprobar solo una fila por atleta/sesión.
- [ ] **ENT-09** Rechazar asistencia anticipada para una sesión futura.
- [ ] **ENT-10** Rechazar POST de atleta ajeno a categorías seleccionadas, inexistente o inactivo.
- [ ] **ENT-11** No registrar automáticamente como «ausente» a atletas sin marca.
- [ ] **ENT-12** Comprobar que la bitácora conserva actor, acción e ID, sin nombres ni datos sensibles.
- [ ] **ENT-13** Verificar rollback si falla escritura o auditoría.
- [ ] **ENT-14** Padre, Junta, utilero y entrenador sin asignación no deben acceder a listado ni detalle ni POST; administrador y director deportivo sí.
- [ ] **ENT-15** Un POST sin token CSRF debe rechazarse.
- [ ] **ENT-16** Verificar rutas, interfaz móvil/escritorio y datos ficticios.
- [ ] **ENT-17** Comprobar impacto del cambio de categoría/estado posterior: pendiente diseño de historial exacto por fecha.
- [ ] **ENT-18** Verificar efecto de zona horaria del servidor en el día operativo local.

## Secuencia siguiente del INC-004

1. Asignación y alcance por entrenador/categoría.
2. Finalización/cancelación controlada de sesiones.
3. Informes privados de asistencia, con histórico por fecha.
4. QA funcional y de autorización antes de pasar al INC-005.


## Validación manual reportada — 21/11/2026

- [x] **ENT-MAN-01 (parcial)** El usuario confirmó que pudo **registrar un entrenamiento** correctamente en su entorno WAMP.
- [ ] **ENT-MAN-02** Confirmar categorías efectivamente asociadas, vista de detalle y persistencia en base de datos (no verificados explícitamente en esta validación).
- [ ] **ENT-MAN-03** Confirmar registro/corrección de asistencia y permisos por rol (todavía no probados).

La validación reportada acredita el flujo de creación de una sesión, pero no cierra por sí sola todas las pruebas ENT-01–18.


## Segundo corte — categorías autorizadas por entrenador

- [ ] **ENT-SCOPE-01** Migración 000011 crea tabla sin modificar las sesiones y atletas existentes.
- [ ] **ENT-SCOPE-02** Administrador institucional puede asignar U7 a un usuario entrenador activo y la relación aparece una sola vez.
- [ ] **ENT-SCOPE-03** No asignar categoría a cuenta sin rol coach, desactivada, categoría inválida o inactiva.
- [ ] **ENT-SCOPE-04** Director deportivo, entrenador, utilero, Junta y padre no pueden acceder a la administración de asignaciones (GET y POST directos).
- [ ] **ENT-SCOPE-05** Entrenador U7 ve sesiones que incluyen U7, no sesiones solo de U9; no ve botón para programar.
- [ ] **ENT-SCOPE-06** Sesión U7+U9: entrenador U7 solo ve U7 y sus atletas, sin enumerar los U9 mediante URL directa.
- [ ] **ENT-SCOPE-07** Entrenador U7 no puede marcar atleta U9 aunque falsifique POST y un ID válido; recibirá error sin cambios de DB.
- [ ] **ENT-SCOPE-08** Entrenador U7 puede marcar y corregir la asistencia de U7 desde la fecha de sesión, sin duplicarla.
- [ ] **ENT-SCOPE-09** Entrenador sin categorías asignadas obtiene lista vacía y 403 al intentar abrir sesiones directamente.
- [ ] **ENT-SCOPE-10** Revocar U7 quita inmediatamente visibilidad de entrenador, acceso GET y autorización para nuevos POST U7.
- [ ] **ENT-SCOPE-11** Reasignar U7 restituye permiso sin duplicación; asignaciones y auditoría conservan autor/fecha.
- [ ] **ENT-SCOPE-12** Sin CSRF, POST de asignación y asistencia se rechazan.
- [ ] **ENT-SCOPE-13** Dirección deportiva sigue pudiendo programar sesiones y gestionar asistencia global, sin poder editar las asignaciones.
- [ ] **ENT-SCOPE-14** La lista administrativa de atletas y los registros familiares no se abren con solo rol `coach`.
- [ ] **ENT-SCOPE-15** Revisar sesiones compartidas, cambios de categoría, revocación concurrente y pantalla móvil en WAMP.


## Validación manual reportada — asignaciones (21/11/2026)

- [x] **ENT-SCOPE-MAN-01 (parcial)** El usuario confirmó que creó una cuenta de entrenador y pudo asignarle **dos categorías** mediante la interfaz de administración en WAMP.
- [ ] **ENT-SCOPE-MAN-02** Iniciar sesión como ese entrenador y comprobar que solo ve sesiones y atletas de sus dos categorías, incluso en sesiones multicategoría.
- [ ] **ENT-SCOPE-MAN-03** Verificar que puede marcar asistencia de sus categorías, que no puede marcar la de otras y que la revocación tiene efecto inmediato.

Esta confirmación acredita la operación de alta y asignación en pantalla. No acredita todavía el aislamiento por rol/categoría ni las pruebas negativas ENT-SCOPE-03–15.


## Validación manual reportada — visibilidad del entrenador (21/11/2026)

- [x] **ENT-SCOPE-MAN-04 (parcial)** El usuario confirma que, al acceder como entrenador, puede visualizar el listado de atletas desde el flujo de entrenamientos.
- [ ] **ENT-SCOPE-MAN-05** Confirmar visualmente que el listado contiene exclusivamente atletas de las **dos categorías** asignadas y que no aparecen otros atletas.
- [ ] **ENT-SCOPE-MAN-06** Confirmar que la cuenta de entrenador NO tiene acceso al listado administrativo global `/app/atletas`.
- [ ] **ENT-SCOPE-MAN-07** Confirmar alta/corrección de asistencias, restricción por categoría en POST y efecto de revocar una asignación.

Esta confirmación no demuestra aún aislamiento completo entre categorías ni seguridad de rutas directas.
