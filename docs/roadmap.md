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


## Parking lot de modernización UI/UX

Las propuestas transversales sugeridas durante el INC-003 se registran en [parking lot de plataforma](parking-lot-ux-plataforma.md): SweetAlert2, DataTables, mensajes toast en esquina superior derecha, notificaciones persistentes por usuario y formularios sin recarga mediante AJAX/Fetch. **La ejecución se planifica después del flujo funcional completo de los incrementos**, salvo fallos funcionales o de seguridad que requieran solución inmediata.


## Secuencia aprobada — consentimientos digitales familiares con firma y PDF

**Acuerdo registrado el 21/11/2026:** implementar la interacción directa con el padre, madre o tutor para leer, decidir y firmar los consentimientos institucionales desde el portal; generar un PDF íntegro que incorpore el documento versionado, la decisión, firma manuscrita trazada en pantalla y constancia de auditoría.

**Orden de trabajo, sin alterar los incrementos en curso:**

1. **INC-003** conserva el expediente del atleta y el registro documental manual existente, con evidencia privada, estados, firmante y trazabilidad. El flujo autónomo de firma de familiares no se adelanta.
2. **INC-004 a INC-010** continúan en el orden previsto.
3. **INC-011 — Comunicaciones y documentos institucionales** incorpora solicitudes de consentimiento por adulto destinatario autenticado, lectura obligatoria del texto versionado, decisiones independientes por tipo, captura accesible de firma dibujada (por ejemplo Signature Pad), PDF con texto exacto, registro de decisión y versión, hash de integridad, descarga autorizada y notificación individual. Evaluar envío por correo y por portal; mantener una alternativa presencial para responsables sin cuenta/correo. Integrar con UX-PL-004.
4. **INC-013 — Seguridad y salida a producción** contempla revisión jurídica de firmas electrónicas, protección de datos de menores, aprobaciones institucionales de las plantillas, custodia privada, revocación, control de versiones, auditoría y pruebas de no acceso entre familias. La firma dibujada, por sí sola, no se considera firma electrónica certificada ni una autorización automática de publicación.

**Dependencias:** UX-PL-006 (verificación de parentesco independiente del acceso digital); versión y texto formal aprobado para cada consentimiento; autenticación fuerte del firmante; controles de representación y posibles conflictos entre responsables; autorización específica para publicar. No generar PDF final de consentimiento sin plantilla aprobada ni afirmar validez jurídica automática.

**Estado:** requisito de secuencia registrado, implementación diferida a INC-011; no interrumpir INC-004.


## Navegación y dashboard por perfil — requisito transversal aprobado (22/11/2026)

**Necesidad reportada:** la ruta privada `/app` no da acceso útil a todos los módulos que se van construyendo. El usuario solicita un dashboard especializado para cada perfil, con herramientas, indicadores, accesos y menú acordes a su función.

**Aplicación progresiva:**
- **Corrección inmediata dentro de INC-004:** catálogo central de navegación por capacidades y perfil, enlaces solo a módulos ya disponibles, panel `/app` con tarjetas reales y navegación persistente en el layout privado. Es navegación, **no** reemplaza filtros de rutas, autorización del servicio, aislamiento por categoría ni la verificación familiar.
- **En cada INC-005 a INC-012:** integrar nuevas funciones al catálogo de opciones conforme se implementen, según perfil institucional.
- **Modernización transversal de UI/UX:** diseñar dashboards completos, métricas seguras, componentes responsive, jerarquía de tareas, alertas y accesos rápidos por rol. Se registra como **UX-PL-007**.
- **INC-013:** QA de navegación y filtrado para roles múltiples, padres, utilero, entrenadores, dirección deportiva, Junta y administradores; comprobar que ocultar un menú no sea la única barrera de acceso.

**Perfiles previstos:** fundadores/administradores, director deportivo, entrenadores con categorías asignadas, utilero, responsables familiares, delegado, miembros de Junta/Tesorería y colaboradores de comunicación. Mostrar solamente datos e indicadores del ámbito autorizado; **no** divulgar cobros ordinarios a miembros de Junta sin atribuciones, ni datos de otros menores.

**Estado:** primera navegación contextual incorporada en INC-004; dashboard especializado con indicadores y tareas queda pendiente de diseño e implementación por incremento.


## Panel ejecutivo contextual — primera versión funcional (22/11/2026)

Se incorpora una mejora anticipada del portal `/app` por su impacto directo en la navegación cotidiana de los usuarios. El diseño se orienta a una academia deportiva institucional, moderna y profesional, con una experiencia distinta por perfil.

- Encabezado institucional oscuro, escudo, jerarquía tipográfica, acciones principales y módulos operativos en tarjetas.
- `PortalDashboardService`: indicadores reales y modestos de atletas activos, categorías y sesiones programadas **según permisos**; entrenador limitado a categorías asignadas, administrador/dirección con visión deportiva general, utilero con expedientes autorizados. Otros perfiles tienen resumen sin métricas no aprobadas.
- La navegación sigue usando `PortalNavigationService`; enlaces solamente a acciones existentes. La vista **no** reemplaza la autorización de cada controlador.
- No se inventan métricas financieras, valores de tesorería, porcentajes de asistencia ni cifras de menores fuera de cada ámbito.
- Diseño responsive y estados vacíos. Los módulos INC-005–INC-012 agregarán sus tarjetas e indicadores cuando sus reglas de autorización estén implementadas.

**Pendiente QA WAMP:** confirmar entrenador con dos categorías, dirección deportiva, fundador, utilero, padre y miembros de Junta; probar móviles y desempeño; no marcar como validado sin prueba del usuario.


## Navegación institucional premium — ajuste visual del INC-004 (22/11/2026)

La retroalimentación sobre el dashboard ejecutivo detectó que el navbar anterior (enlaces planos en una fila) no correspondía con la identidad visual profesional y elegante aprobada para la academia.

Se reemplaza el encabezado del layout privado por una navegación institucional en dos niveles:
- Franja de marca con escudo, identidad «Academia Ramón Sánchez», perfil de la sesión, acceso al sitio público y cierre de sesión.
- Barra secundaria azul marino con «Panel principal» y menús agrupados por área funcional. Los desplegables se alimentan de `PortalNavigationService` y por tanto muestran exclusivamente las opciones permitidas al usuario.
- Indicador visual de sección actual, estilos de foco y menú con `details/summary` operable por teclado, sin JavaScript obligatorio.
- Diseño responsive: distribución de navegación en varias líneas para móviles y menús desplegables adaptados; no se agregan controles decorativos sin función.
- No sustituye controles de servidor y no muestra acciones futuras que aún no existan.

Pruebas de aceptación en `docs/incrementos/INC-004-entrenamientos-asistencia/pruebas-pendientes.md`. La navegación puede perfeccionarse con el diseño de dashboards definitivos de UX-PL-007.
