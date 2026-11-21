# Parking lot — mejoras transversales de experiencia, tablas y notificaciones

**Fecha de registro:** 2026-11-21  
**Origen:** retroalimentación durante INC-003 de la Academia Integral de Fútbol Profesor Ramón Sánchez.  
**Prioridad de ejecución:** después de completar el flujo funcional de incrementos INC-001 a INC-013, salvo correcciones de seguridad/usabilidad indispensables para que un incremento funcione. **No equivale a aprobación de librerías, APIs ni arquitectura definitiva.**

## Objetivo de diseño

Conservar una plataforma institucional **elegante, moderna, profesional, consistente y responsive**, sin que los formularios y operaciones administrativas se sientan como navegación de páginas sueltas. Mantener accesibilidad, seguridad del servidor y trazabilidad por encima de los efectos visuales.

## UX-PL-001 — SweetAlert2 para confirmaciones y mensajes

**Idea:** integrar SweetAlert2 para éxitos, advertencias, errores de validación, confirmación de acciones destructivas y tareas administrativas.  
**Criterios:** plantillas y utilidades centralizadas, español, colores del diseño, iconografía consistente, foco accesible, teclado, lectores de pantalla; no mostrar detalles técnicos ni datos personales. No sustituir las comprobaciones del servidor.
**Módulos candidatos:** invitaciones, cambios de roles, inscripción/edición, asistencia, uniformes y cobros.
**Estado:** parking lot.

## UX-PL-002 — DataTables para registros administrativos

**Idea:** usar DataTables (con jQuery ya contemplado en el proyecto) en las tablas de gestión.  
**Criterios:** búsquedas, ordenación, paginación, estado vacío, filtros por categoría/estado, formato responsive, internacionalización al español, accesibilidad y exportación solo cuando se autorice.
**Seguridad:** filtros, alcance y permisos **en el backend**, paginación y búsqueda server-side cuando crezca la información, sin enviar expedientes completos ni datos sensibles al navegador.
**Módulos candidatos:** atletas, personas, consultas, invitaciones, torneos y asistencias.
**Estado:** parking lot.

## UX-PL-003 — Notificaciones toast en esquina superior derecha

**Idea:** feedback discreto, autoocultable, para guardado correcto, envío, actualización, fallo recuperable e información no crítica.
**Criterios:** reutilizar un componente central con distintas variantes; evitar que los toasts contengan información de menores o revelen datos sensibles; respetar preferencias de movimiento, duración adecuada y zona `aria-live`.
**Separación:** una notificación toast es **feedback inmediato** de una operación, no una notificación persistente del sistema.
**Estado:** parking lot.

## UX-PL-004 — Notificaciones persistentes para usuarios

**Idea:** centro de notificaciones con campana en portal, bandeja personal, leído/no leído, prioridades y preferencias; avisos de entrenamientos, convocatorias, asuntos administrativos autorizados y comunicaciones a familias.
**Diseño futuro:** `notifications`, `notification_recipients`, preferencias, estados, caducidad, canal permitido, generación de eventos de dominio, deduplicación y auditoría. Considerar correo / notificaciones web solo cuando existan consentimiento, capacidad y propósito aprobados.
**Seguridad:** autorización por destinatario, minimización de datos de menores, no notificar información de terceros ni contenido disciplinario sensible, reglas de visibilidad específicas por rol y por relación familiar verificada.
**No confundir:** campana/bandeja persistente vs. toast efímero.
**Estado:** parking lot; evaluar al implementar comunicaciones INC-011 y endurecimiento INC-013, sin adelantar compromisos.

## UX-PL-005 — Formularios asíncronos AJAX / Fetch

**Idea:** que altas, ediciones y acciones se ejecuten sin recarga de página, con indicadores de progreso, validación por campo y actualización parcial de registros; el usuario permanece en el contexto de trabajo.
**Arquitectura futura:** contratos JSON uniformes `{success, message, errors, data}`; HTTP apropiados; formularios progresivamente mejorados, CSRF renovado, prevención de doble envío, control de estados, reintentos seguros, manejo de sesión caducada y errores 401/403/422; sin deshabilitar controles del backend ni depender únicamente de JavaScript. Evaluar un módulo ligero reutilizable con Fetch/jQuery, según estándares del proyecto.
**Criterios UX:** feedback claro mediante SweetAlert2/toast cuando corresponda, desactivar botón durante envío, conservar valores ante error, no perder foco ni accesibilidad, permitir rutas completas por fallback.
**Estado:** parking lot para modernización transversal posterior al flujo de incrementos, sin impedir las pruebas de los formularios actuales.

## Priorización y decisiones técnicas futuras

| ID | Impacto | Dependencias | Momento propuesto |
|---|---|---|---|
| UX-PL-001 | Alto | Convenciones de mensajes, accesibilidad y diseño | Refactor transversal después del flujo |
| UX-PL-002 | Alto | Contratos de tablas, backend filtrado por permisos | Refactor transversal después del flujo |
| UX-PL-003 | Medio-alto | UX-PL-001/estándares de mensajes | Refactor transversal después del flujo |
| UX-PL-004 | Alto, mayor complejidad | INC-011, seguridad por destinatario y preferencias | Diseño tras módulos funcionales |
| UX-PL-005 | Alto | Controladores JSON, CSRF, UX-PL-001/003 | Refactor transversal después del flujo |

## Criterios de aceptación de la modernización

- [ ] Librerías y licencias evaluadas con versiones vigentes, mantenimiento y riesgos de seguridad.
- [ ] Componentes reutilizables, sin código duplicado en cada formulario o tabla.
- [ ] Compatibilidad con CodeIgniter 4, Shield, Tailwind/CSS institucional y navegadores objetivos.
- [ ] Experiencia de pantalla sin recarga para operaciones comunes, con fallback accesible.
- [ ] Mensajes/errores consistentes en español sin exponer datos personales.
- [ ] Tests de autorización, CSRF, 403/404, expiración de sesión y doble submit con AJAX.
- [ ] Notificaciones dirigidas solo a destinatarios válidos con control de lectura y auditoría, si se implementa la campana.
- [ ] Rendimiento y comportamiento en móviles con conjuntos grandes de datos comprobados.
- [ ] Revisión con fundadores y dirección deportiva antes de expandir a toda la plataforma.

## Regla del parking lot

Cualquier propuesta de modernización añadida aquí **no interrumpe por sí sola** el orden de incrementos ni crea deuda técnica invisible. Las correcciones críticas de funcionamiento y seguridad se resuelven en el incremento vigente. Cuando se termine el flujo principal, priorizar este inventario como un proyecto formal de mejora de experiencia.


## UX-PL-006 — Separar verificación familiar de vinculación de cuenta digital

**Origen:** mejora detectada por el usuario al registrar a una madre como segunda responsable de un atleta durante el INC-003 (21/11/2026). Esta es la mejora a la que se refería cuando solicitó enviarla al parking lot; el registro anterior era demasiado genérico.

**Problema observado:** la pantalla `/app/familias/verificar` exige el correo electrónico de una **cuenta activada** para verificar la relación del adulto con el atleta. En el proceso real, el padre o la madre puede estar registrado como responsable **sin tener correo ni cuenta en el portal**. Se han combinado indebidamente dos decisiones independientes.

**Mejora requerida (para análisis e implementación posterior):**
1. Permitir registrar al padre, madre u otro responsable autorizado sin requerir correo ni cuenta.
2. Separar **validación de identidad/parentesco o responsabilidad legal** de **habilitación de acceso digital**.
3. Permitir verificar administrativamente la relación familiar sin crear una cuenta Shield.
4. Cuando el adulto quiera utilizar el portal, ofrecer un proceso adicional y opcional de invitación por correo, activación de cuenta y asociación controlada con la persona previamente verificada.
5. Admitir escenarios en los que solo un padre tenga cuenta, ambos tengan cuentas individuales o ninguno tenga acceso digital.
6. Mantener la revocación independiente del acceso digital y de la condición de responsable; auditar quién valida, quién vincula y quién revoca.
7. Conservar el principio de que **no se concede acceso a información de menores por compartir apellido, conocer un correo o simplemente estar registrado**.

**Situación actual:** el formulario de verificación exige correo; **no utilizar datos ficticios, el correo del otro progenitor ni una cuenta ajena para sortear el requisito**. El adulto puede permanecer registrado con relación pendiente de verificación digital hasta rediseñar este flujo.

**Estado:** parking lot — requerimiento ya identificado, pendiente de modelado, validación institucional, implementación y pruebas de seguridad. No interrumpir el flujo de incrementos por esta mejora; reconsiderar el alcance antes de producción porque afecta a las autorizaciones sobre datos de menores.

**Pruebas futuras:** verificar responsable sin correo; cuenta no creada; invitación posterior; ambos padres con cuentas distintas; uno sin cuenta; revocación del acceso sin borrar parentesco; no acceso entre familias.
