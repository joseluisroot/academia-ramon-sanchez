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
- [x] **ENT-SCOPE-MAN-05 (visual)** El usuario confirma el 22/11/2026 que el entrenador **solo visualiza jugadores de las categorías asignadas**. Queda pendiente la prueba negativa de acceso directo/POST a otras categorías.
- [ ] **ENT-SCOPE-MAN-06** Confirmar que la cuenta de entrenador NO tiene acceso al listado administrativo global `/app/atletas`.
- [ ] **ENT-SCOPE-MAN-07** Confirmar alta/corrección de asistencias, restricción por categoría en POST y efecto de revocar una asignación.

Esta confirmación no demuestra aún aislamiento completo entre categorías ni seguridad de rutas directas.


## Tercer bloque — cierre y reportes (pendiente de ejecución WAMP)

- [ ] **ENT-CLOSE-01** Migración 000012 agrega categoría nullable y tabla de historial sin perder asistencias previas.
- [ ] **ENT-CLOSE-02** Una sesión futura no se puede finalizar, aunque intente POST directo.
- [ ] **ENT-CLOSE-03** No se permite finalizar sesión sin ninguna marca.
- [ ] **ENT-CLOSE-04** Una sesión del día con al menos una marca se finaliza; se registra historial y auditoría.
- [ ] **ENT-CLOSE-05** Tras finalizar, supervisor y entrenador reciben rechazo al tratar de cambiar una marca por POST.
- [ ] **ENT-CLOSE-06** Solo sesión sin marcas puede cancelarse; una sesión con asistencia no.
- [ ] **ENT-CLOSE-07** Entrenador no puede cerrar ni cancelar mediante POST directo; tampoco otros roles sin permiso.
- [ ] **ENT-CLOSE-08** Sesión finalizada/cancelada no puede finalizarse o cancelarse de nuevo.
- [ ] **ENT-CLOSE-09** Un atleta sin marca queda sin marcar, nunca convertido automáticamente en ausente.
- [ ] **ENT-CLOSE-10** El reporte presenta conteos solamente de sesiones finalizadas y categoría histórica de las nuevas marcas.
- [ ] **ENT-CLOSE-11** Entrenador con U7 no ve marcas históricas U9, aun cuando la sesión original fuera U7+U9.
- [ ] **ENT-CLOSE-12** Entrenador U7 con categoría revocada deja de acceder a su reporte U7.
- [ ] **ENT-CLOSE-13** Marcas anteriores a 000012 con categoría nula no se atribuyen falsamente a una categoría; no se pierden de DB.
- [ ] **ENT-CLOSE-14** Un cambio de categoría del atleta no altera el campo histórico de marcas ya insertadas.
- [ ] **ENT-CLOSE-15** POST sin CSRF válido queda rechazado; consultar y acceder por URL directa a reporte también requiere permiso.
- [ ] **ENT-CLOSE-16** Pruebas con dos operadores cerrando simultáneamente: solo una transición debe prevalecer.
- [ ] **ENT-CLOSE-17** Comprobar responsive móvil y textos de advertencia.
- [ ] **ENT-CLOSE-18** Antes de premiación anual, definir denominador para porcentaje de asistencia, criterios de sesiones válidas y tratamiento de excusas.

**Cierre INC-004 pendiente:** pruebas de permisos por categoría y POST manipulados, plantilla/roster histórico por entrenamiento, política de reapertura y QA en WAMP. No hacer merge ni considerar estadísticas aptas para premios anuales hasta resolverlo.


## Cuarto bloque — plantilla histórica al cierre

- [ ] **ENT-ROSTER-01** Migración 000013 crea `training_session_roster` sin alterar asistencias previas.
- [ ] **ENT-ROSTER-02** Finalizar sesión ficticia con atletas activos de dos categorías: captura una única fila por atleta.
- [ ] **ENT-ROSTER-03** Atleta con marca registrado en categoría diferente a la actual: la plantilla conserva categoría de la marca.
- [ ] **ENT-ROSTER-04** Atleta sin marca permanece sin marca, no se convierte automáticamente en ausente.
- [ ] **ENT-ROSTER-05** Tras cambiar la categoría o poner inactivo un atleta ficticio, el detalle de sesión finalizada mantiene su categoría de la plantilla capturada.
- [ ] **ENT-ROSTER-06** Entrenador U7 no puede consultar la plantilla U9 de una sesión U7+U9 mediante detalle ni POST manipulado.
- [ ] **ENT-ROSTER-07** Cancelación no crea plantilla. Fallo al crear plantilla revierte cierre e historial.
- [ ] **ENT-ROSTER-08** Sesión finalizada antes de migración 000013 queda sin plantilla histórica y se preservan marcaciones en base de datos.
- [ ] **ENT-ROSTER-09** Validar que captura de roster al cierre difiere de la plantilla real a fecha de sesión en caso de altas/bajas tardías; no usar para porcentajes oficiales.
- [ ] **ENT-ROSTER-10** Probar simultaneidad de cierre y cambios de marcaciones; verificar consistencia y evitar duplicados.


## INC-004-HIS-001 — regresiones de historial y navegación (pendiente WAMP)

- [ ] **HISBUG-01** Tras actualizar rama, la sesión U9 ya cerrada conserva la marca «Presente» y muestra el aviso de historial anterior si no existe roster.
- [ ] **HISBUG-02** Una sesión de cierre posterior a 000013 muestra «Categoría histórica» y atletas congelados.
- [ ] **HISBUG-03** Sesión cerrada antes de 000013: las marcas con categoría histórica se muestran; marca con categoría nula se identifica como «sin verificar» únicamente si la sesión tenía una categoría.
- [ ] **HISBUG-04** En sesión multicategría, entrenador U7 no ve marcas, totales ni listado U9 aunque visite URL directa.
- [ ] **HISBUG-05** La fecha de cierre almacenada como UTC se visualiza con hora local El Salvador; se evalúa la zona de registros anteriores si el servidor no era UTC.
- [ ] **HISBUG-06** `/app` muestra enlaces de entrenamiento y reporte según cuenta; el menú del layout también permite llegar a esos módulos.
- [ ] **HISBUG-07** Padre, utilero, entrenador, Junta y fundador ven únicamente opciones permitidas, sin acceso a otros menores por URL.
- [ ] **HISBUG-08** No se introducen regresiones de CSRF, sesión, autorización y rendimiento con consultas de menú.


## Panel ejecutivo contextual — validaciones pendientes

- [ ] **DASH-01** Administración institucional ve indicadores deportivos de la academia y enlaces reales a entrenamientos, expedientes y verificaciones permitidas.
- [ ] **DASH-02** Entrenador con dos categorías ve contadores solo de sus atletas, categorías y sesiones; nunca se incluyen otras categorías.
- [ ] **DASH-03** Dirección deportiva ve las sesiones y sus indicadores generales, pero no una opción para asignar entrenadores si no tiene ese permiso.
- [ ] **DASH-04** Padre ve panel familiar sin cifras administrativas ni datos de otras familias.
- [ ] **DASH-05** Junta/Tesorería no reciben indicadores financieros falsos ni acceso a matrícula o mensualidades.
- [ ] **DASH-06** Utilero ve únicamente sus funciones de registro y atención.
- [ ] **DASH-07** Cuenta sin módulos operativos muestra estado vacío sin enlaces prohibidos; permisos múltiples no elevan privilegios.
- [ ] **DASH-08** Pantallas de 375px, tablet y escritorio sin desbordamiento, botones accionables y navegación por teclado.
- [ ] **DASH-09** GET/POST a rutas no autorizadas continúa bloqueado pese al cambio visual; no se infiere autorización de un enlace.
- [ ] **DASH-10** Conteos de sesiones compartidas U7+U9 no se duplican para el entrenador que tiene ambas categorías.


## Navbar institucional ejecutivo — pruebas pendientes WAMP

- [ ] **NAV-01** El encabezado muestra el escudo, marca institucional, nombre/perfil autenticado y enlaces funcionales al sitio público y cierre de sesión.
- [ ] **NAV-02** Administrador ve menús deportivos, expedientes/familias, atención y accesos conforme a sus permisos efectivos.
- [ ] **NAV-03** Entrenador ve menú de entrenamientos y reporte, sin acceso a expedientes globales, categorías de otros entrenadores ni administración de permisos.
- [ ] **NAV-04** Padre/tutor, utilero y Junta únicamente ven las opciones autorizadas para su cuenta.
- [ ] **NAV-05** Se destaca la sección activa sin atribuir estado «página actual» a un enlace genérico incorrecto.
- [ ] **NAV-06** Menús desplegables abren con mouse, teclado y pantalla táctil; controles de foco accesibles; enlaces navegables.
- [ ] **NAV-07** Responsive en 375px, tablet y escritorio: sin superposición del contenido, desplegables utilizables, cierre de sesión visible.
- [ ] **NAV-08** Ocultar enlaces no permite acceso a URL directa no autorizada (HTTP 403 o rechazo previsto en rutas y servicios).
- [ ] **NAV-09** Usuarios con más de un rol combinan solo opciones que efectivamente tienen autorizadas.
- [ ] **NAV-10** No aparecen módulos «próximamente» en la navegación como enlaces inválidos.
