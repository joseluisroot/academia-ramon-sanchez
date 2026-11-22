# INC-003 — Control QA

Todas estas pruebas están **pendientes de ejecución en WAMP**. Registrar fecha, rol empleado, resultado y evidencia sin exponer datos personales.

## Migraciones y catálogo

- [ ] **DAT-01** Crear tablas `sports_categories`, `athletes`, `athlete_guardians`.
- [ ] **DAT-02** Seed U7, U9, U11, U13, U17; segunda ejecución no duplica categorías.
- [ ] **DAT-03** FKs impiden asociar atletas/personas inexistentes.
- [ ] **DAT-04** Restricción única impide relacionar dos veces al mismo adulto con un atleta.

## Registro administrativo

- [x] **ATL-01 (parcial)** El usuario confirmó la creación de su primer atleta en WAMP el 21/11/2026; no se confirmó aún la asociación efectiva del adulto ni la auditoría.
- [ ] **ATL-02** Utilero con `athletes.manage` puede registrar atleta.
- [ ] **ATL-03** Padre `guardian` no puede consultar listado administrativo ni crear atleta.
- [ ] **ATL-04** Fecha inválida/futura, fecha ingreso anterior a nacimiento, nombres vacíos y categoría no válida: rechazadas.
- [ ] **ATL-05** Alta sin teléfono es válida.
- [ ] **ATL-06** Un adulto existente puede reutilizarse para otro hijo sin duplicación.
- [ ] **ATL-07** Error de inserción revierte persona, atleta y relación.
- [ ] **ATL-08** Bitácora guarda actor y ID de atleta, pero no nombres ni datos de niños.

## Verificación de relaciones

- [ ] **REL-01** La relación recién creada permanece sin verificar y no da acceso al padre (siguiente prueba).
- [ ] **REL-02** Administrador verifica identidad fuera del sistema, acepta y enlaza cuenta familiar.
- [ ] **REL-03** Checkbox sin marcar se rechaza.
- [ ] **REL-04** Correo incorrecto/inexistente/cuenta inactiva: rechazo.
- [ ] **REL-05** Cuenta sin rol `guardian`: rechazo.
- [ ] **REL-06** Relación ya verificada: no admite reautorización.
- [ ] **REL-07** Adulto vinculado a otra cuenta: bloqueo.
- [ ] **REL-08** Acceso de utilero/entrenador/guardián al módulo de verificación: denegado.
- [ ] **REL-09** Registro auditado de cuenta autorizante y relación verificada.

## Acceso familiar y UX

- [ ] **FAM-01** Sin vínculo verificado: `/app/mis-atletas` muestra estado vacío.
- [ ] **FAM-02** Padre ve únicamente atleta vinculado.
- [ ] **FAM-03** Cuenta de otro padre no ve ese atleta.
- [ ] **FAM-04** Un padre con varios hijos vinculados ve únicamente sus hijos verificados.
- [ ] **FAM-05** URL no válida no permite enumerar expedientes de otros menores.
- [ ] **FAM-06** Prueba de móvil y escritorio con nombres extensos.
- [ ] **FAM-07** Confirmar mensaje claro cuando vinculación aún está pendiente.

## Preproducción

- [ ] **SEC-01** Auditoría de todos los cambios sensibles, sin datos identificativos de menores.
- [ ] **SEC-02** Definir proceso de revocación de vínculo y disputas de custodia.
- [ ] **SEC-03** Aprobar rango de edades, corte de temporada y categoría.
- [ ] **SEC-04** Confirmar retención de datos de menores y consentimiento de sus responsables.
- [ ] **SEC-05** Revisar compatibilidad de PHP/CI4/MySQL y pruebas de integración.

**Criterio:** no fusionar ni habilitar producción sin revisión de controles parentales, migraciones y permisos.


## Segundo corte — edición e historial (pendiente)

- [ ] **HIS-01** Migrar atleta ya inscrito y crear una sola fila de categoría inicial con fecha de ingreso.
- [ ] **HIS-02** Inscribir nuevo atleta y guardar categoría inicial dentro de la transacción.
- [ ] **HIS-03** Editar solo nombre/apellido sin duplicar fila de historial de categoría.
- [ ] **HIS-04** Cambiar categoría y registrar historial, fecha, actor y auditoría.
- [ ] **HIS-05** Rechazar categoría inexistente o inactiva, campos inválidos y cambios sin modificaciones.
- [ ] **HIS-06** Rechazar edición sin `athletes.manage`, incluso si se intenta POST manual.
- [ ] **HIS-07** Rechazar ruta de atleta inexistente con HTTP 404, sin revelar datos.
- [ ] **HIS-08** Simular fallo al insertar historial y comprobar rollback del cambio de categoría.
- [ ] **HIS-09** Confirmar que la modificación no altera vínculos familiares, fecha de nacimiento ni fecha de ingreso.
- [ ] **HIS-10** Verificar consistencia móvil/escritorio en el formulario y el historial.

## Tercer corte — dorsales y familiares múltiples (pendiente)

- [ ] **DOR-01** Ejecutar migración 000007 sin errores y sin borrar atletas existentes.
- [ ] **DOR-02** Asignar dorsal 7 a un atleta U7: queda vigente y se registra historial con actor.
- [ ] **DOR-03** Rechazar asignar dorsal 7 a otro atleta U7.
- [ ] **DOR-04** Permitir dorsal 7 a un atleta de U9 (categoría distinta).
- [ ] **DOR-05** Reemplazar dorsal actual y registrar liberación + nueva asignación en historial.
- [ ] **DOR-06** Liberar dorsal; queda disponible para otro atleta de la misma categoría.
- [ ] **DOR-07** Rechazar dorsales fuera del rango provisional 1–99, duplicados y solicitudes inválidas.
- [ ] **DOR-08** Cambiar categoría con dorsal vigente: bloquear; tras liberarlo: permitir cambio con historial.
- [ ] **DOR-09** Comprobar transacción y rollback si falla escritura de historial.
- [ ] **DOR-10** Usuario guardian o Junta sin `athletes.manage`: no puede asignar ni liberar por POST directo.
- [ ] **DOR-11** Dos operadores asignando mismo dorsal simultáneamente: solo una asignación prevalece.

- [ ] **RES-01** Añadir segundo adulto existente a atleta ficticio sin duplicarlo en `persons`.
- [ ] **RES-02** Añadir un adulto nuevo desde el expediente y comprobar relación sin verificar.
- [ ] **RES-03** Rechazar el mismo adulto dos veces para un atleta.
- [ ] **RES-04** Comprobar que un segundo adulto sin vínculo verificado **no** ve el expediente.
- [ ] **RES-05** Administrador autorizado verifica el vínculo de segundo responsable y la cuenta solo ve los atletas correspondientes.
- [ ] **RES-06** Revocar una relación verificada y comprobar desaparición inmediata de ese atleta en `/app/mis-atletas` para esa cuenta.
- [ ] **RES-07** Revocar vínculo de un hijo no elimina el acceso a otros hijos verificados de ese adulto.
- [ ] **RES-08** Utilero/guardian no puede revocar vínculo, ni mediante petición POST manual.
- [ ] **RES-09** La relación permanece registrada como pendiente tras revocar y puede verificarse nuevamente solo mediante proceso autorizado.
- [ ] **RES-10** Auditar actor y tipo de cambio, sin nombres ni datos personales de niños.
- [ ] **RES-11** Probar móvil y escritorio con varios responsables y muchos registros.

**Decisiones de negocio sin aprobar:** rango de dorsales 1–99, política de reserva/liberación, conservación al cambiar categoría, número de responsables reconocidos, procedimiento ante desacuerdo/custodia y requisitos documentales para autorizar/revocar.


## Incidencia INC-003-CSRF-001 — formularios múltiples en ficha del atleta

**Reportada:** 21/11/2026. **Ruta de operación:** `POST /app/atletas/1/responsables`. **Síntoma:** `CodeIgniter\\Security\\Exceptions\\SecurityException: The action you requested is not allowed` (`Security.php:256`).

**Diagnóstico sobre el código:** `app/Config/Filters.php` aplica `csrf` globalmente; `app/Views/app/athletes/edit.php` incluye `csrf_field()` en cada formulario y `app/Config/Security.php` tenía `csrfProtection = 'session'` y `regenerate = true`. Al renovar el token tras otro POST, los formularios ya abiertos o múltiples pestañas pueden quedar con tokens obsoletos. Es una **causa probable**, todavía no confirmada por ejecución local.

**Corrección aplicada en rama:** cambiar únicamente `regenerate` a `false`. La validación CSRF global, el token secreto por sesión y la verificación de autorización de cada POST **siguen activados**. No se añaden excepciones CSRF para rutas de responsables.

- [ ] **CSRF-01** Actualizar código, cerrar las páginas viejas y recargar `/app/atletas/1/editar` para obtener token vigente.
- [ ] **CSRF-02** Seleccionar adulto existente, completar relación y enviar: crear solo una relación pendiente, sin excepción CSRF.
- [ ] **CSRF-03** Sin recargar la página, enviar un segundo formulario distinto (p. ej. dorsal o nuevo adulto): debe validar CSRF normalmente.
- [ ] **CSRF-04** Repetir flujo con dos pestañas abiertas en la **misma sesión** y con operaciones distintas.
- [ ] **CSRF-05** Un POST sin token y con token arbitrario se rechaza: la protección CSRF permanece habilitada.
- [ ] **CSRF-06** Usuario `guardian` no puede enviar `POST /app/atletas/1/responsables`, incluso con CSRF válido.
- [ ] **CSRF-07** Formulario sobre página abierta antes del cambio: recargar y repetir; no utilizar HTML cacheado ni sesiones anteriores.
- [ ] **CSRF-08** Comprobar si el error reaparece; si ocurre, revisar cookies de sesión, dominio/puerto, token enviado y logs de CodeIgniter, sin divulgar valores secretos.

**Importante:** no considerar confirmada la corrección hasta que el usuario la pruebe en WAMP. Revaluar regeneración y manejo AJAX de CSRF durante la modernización transversal (UX-PL-005).


## Operación administrativa de retiro de responsables pendientes

- [ ] **RES-12** Administrador con `family.links.verify` ve **Quitar pendiente** solo en adultos adicionales no verificados.
- [ ] **RES-13** Al quitar pendiente, el atleta conserva a su responsable principal y la persona adulta sigue en `persons`.
- [ ] **RES-14** El adulto retirado puede seleccionarse de nuevo y su nueva relación queda pendiente.
- [ ] **RES-15** No se puede quitar mediante la ruta de retiro una relación ya verificada ni una relación principal.
- [ ] **RES-16** Usuario sin `family.links.verify` obtiene 403 en POST directo; token CSRF inválido se rechaza.
- [ ] **RES-17** POST con relación de otro atleta no modifica datos ni relaciones.
- [ ] **RES-18** Se registra ID, autor y acción en auditoría sin nombres; no se borra la persona.
- [ ] **RES-19** Tras retirar una relación pendiente desaparece de la pantalla de verificación, conservando los vínculos verificados del atleta.


### Validación manual confirmada — 21/11/2026

- [x] **RES-MAN-01** El usuario confirmó en WAMP que desde el expediente del atleta puede **agregar** un adulto responsable y **quitar** una relación de adulto pendiente.
- [ ] **RES-MAN-02** Confirmar en datos que quitar la relación no elimina a la persona de `persons` (no verificado todavía con inspección de BD).
- [ ] **RES-MAN-03** Confirmar que el adulto retirado puede vincularse otra vez al mismo atleta sin duplicar la persona; no inferirlo de la validación anterior.

Esta validación comprueba la operación visible, pero **no cierra** aún RES-12–19: siguen pendientes las pruebas negativas de autorización, CSRF, protección del responsable principal, auditoría y aislamiento entre familias.
