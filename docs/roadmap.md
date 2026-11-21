# Plan maestro de incrementos

El alcance es **integral**: sitio público y portal privado. La división por incrementos no elimina ningún módulo.

| Incremento | Entregables principales | Depende de |
|---|---|---|
| INC-001 | Fundamentos, autenticación, permisos, auditoría, layouts y tokens visuales | Inicial |
| INC-002 | Sitio público base, contenido institucional y contacto | INC-001 |
| INC-003 | Personas, atletas, responsables, validación, categorías, dorsales | INC-001 |
| INC-004 | Sesiones, entrenamientos y asistencia | INC-003 |
| INC-005 | Torneos, calendarios, delegaciones y convocatorias | INC-003 |
| INC-006 | Resultados, tablas, goles y estadísticas | INC-005 |
| INC-007 | Perfiles públicos autorizados e insignias | INC-003, INC-006 |
| INC-008 | Matrícula, mensualidades, mora y comprobantes digitales | INC-003 |
| INC-009 | Pedidos, pago y fabricación de uniformes | INC-003, INC-008 |
| INC-010 | Fondo de Junta, caja, gastos, anticipos e informes | INC-001 |
| INC-011 | Comunicados, casos y documentos institucionales | INC-003 |
| INC-012 | Contenido editorial, biblioteca multimedia y promoción | INC-002, INC-003 |
| INC-013 | Endurecimiento, QA integral, respaldo, despliegue en hosting | Todos |

Numeración y secuencia revisables si las dependencias lo aconsejan.

## Regla de rama

`feature/inc-NNN-descripcion`. Cada rama incluye:
- Documentación específica en `docs/incrementos/INC-NNN-descripcion/`.
- Migraciones, seeders, models, controllers, servicios, filtros, vistas y rutas **cuando correspondan**.
- Pruebas, criterios de aceptación, plan de despliegue y cambios de configuración.
- Pull request para revisión; no hacer merge directo a `main`.

## Definition of Done

1. Requerimientos y casos de uso trazables.
2. Reglas y permisos verificados en servidor.
3. Migraciones reproducibles y datos de prueba ficticios.
4. Interfaz responsive y consistente.
5. Pruebas de aceptación y regresión satisfactorias.
6. Documentación y bitácora del incremento actualizadas.
7. Revisión en pull request completada.
