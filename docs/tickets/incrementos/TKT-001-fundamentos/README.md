# TKT-001 — Fundamentos
Implementación inicial: flag fail-closed, lista explícita de rutas/métodos durante prelanzamiento, catálogo Shield, grupo ticket_operator sin portal.access, workspace protegido y navegación contextual para usuarios del portal completo. Ruta GET /tickets; se reutiliza /login de Shield. No hay operaciones de negocio ni migraciones.
Los permisos futuros están definidos pero no implementan acciones; alcance por evento se incorpora con TKT-002. No asignar permisos de negocio reales todavía. El grupo operador solo tiene tickets.access. El administrador institucional recibe catálogo de permisos, sujeto a revisión institucional.
En prelaunch se permite autenticación Shield existente, incluido enlace mágico y acciones de autenticación; no se habilita registro ni activación de invitaciones. Provisionar cuentas mediante procedimiento autorizado existente antes de habilitar producción; revisar redirecciones efectivas de Settings/Shield, incluyendo cuentas existentes sin acceso al módulo, login/logout y flujos MFA. No introducir credenciales de prueba en Git.
## Configuración local
academy.launchMode = prelaunch
academy.modules.tickets.enabled = true
Abrir /tickets con cuenta institucional existente. Apagar flag para comprobar cierre. No habilitar en producción todavía.
## QA pendiente
composer test; php spark routes; php spark filter:check get /tickets; verificar manualmente matriz en pruebas.md. No se han ejecutado pruebas en WAMP desde el conector GitHub. Revisar caché global (ninguna ruta tickets debe activar cachePage), HTTPS efectivo y permisos de producción.
