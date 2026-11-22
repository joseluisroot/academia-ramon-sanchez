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
