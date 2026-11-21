# INC-003 — Control QA

Todas estas pruebas están **pendientes de ejecución en WAMP**. Registrar fecha, rol empleado, resultado y evidencia sin exponer datos personales.

## Migraciones y catálogo

- [ ] **DAT-01** Crear tablas `sports_categories`, `athletes`, `athlete_guardians`.
- [ ] **DAT-02** Seed U7, U9, U11, U13, U17; segunda ejecución no duplica categorías.
- [ ] **DAT-03** FKs impiden asociar atletas/personas inexistentes.
- [ ] **DAT-04** Restricción única impide relacionar dos veces al mismo adulto con un atleta.

## Registro administrativo

- [ ] **ATL-01** Administrador institucional puede registrar atleta con adulto nuevo.
- [ ] **ATL-02** Utilero con `athletes.manage` puede registrar atleta.
- [ ] **ATL-03** Padre `guardian` no puede consultar listado administrativo ni crear atleta.
- [ ] **ATL-04** Fecha inválida/futura, fecha ingreso anterior a nacimiento, nombres vacíos y categoría no válida: rechazadas.
- [ ] **ATL-05** Alta sin teléfono es válida.
- [ ] **ATL-06** Un adulto existente puede reutilizarse para otro hijo sin duplicación.
- [ ] **ATL-07** Error de inserción revierte persona, atleta y relación.
- [ ] **ATL-08** Bitácora guarda actor y ID de atleta, pero no nombres ni datos de niños.

## Verificación de relaciones

- [ ] **REL-01** La nueva relación está sin verificar y no da acceso al padre.
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
