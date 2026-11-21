# INC-001 — Autenticación y permisos con CodeIgniter Shield

**Estado:** código incorporado en rama; pendiente de ejecutar y validar en WAMP.

## Decisiones de seguridad

- Se integra la dependencia oficial `codeigniter4/shield` en `composer.json`.
- El registro autónomo queda desactivado en `Config\Auth::$allowRegistration` y las rutas de `register` se excluyen explícitamente.
- Shield proporciona autenticación por sesión, formularios de login y enlace mágico de recuperación.
- El formulario de login utiliza la identidad gráfica de la academia.
- La ruta `/app` exige **sesión** y permiso **`portal.access`**. La autorización también se verifica en el controlador.
- CSRF emplea sesiones y se aplica como filtro global; los intentos de autenticación tienen filtro de limitación de tasa.
- No se crean usuarios ni contraseñas predeterminados en seeders.

## Permisos iniciales

Catálogo de grupos/permiso en `app/Config/AuthGroups.php`.

- `institutional_admin`: fundadores y Dennis; gestión integral operativa.
- `access_manager`: **una persona autorizada** para otorgar roles e invitaciones.
- Dirección deportiva, entrenador, utilero, tesorero de Junta, miembro Junta, colaborador audiovisual, familia y soporte técnico: accesos específicos.
- Los permisos por rol **no sustituyen** la verificación por atleta, categoría, tesorería o documento; se implementará en cada servicio de dominio.

## Acceso institucional inicial

La primera cuenta será creada manualmente por un operador autorizado en la instalación **local** mediante comandos Shield. No crear contraseñas de demostración ni cuentas administrativas desde HTTP.

```powershell
php spark shield:user create -n usuario_institucional -e persona@example.invalid -g institutional_admin
php spark shield:user addgroup -n usuario_institucional -g access_manager
```

**Sustituir ambos valores por los datos reales solo en el entorno local**, nunca en archivos de Git. El comando puede solicitar contraseña en la terminal. Asegurarse de designar expresamente al responsable de accesos antes de asignar `access_manager`.

## Funcionalidad deliberadamente pendiente

- La invitación por correo con enlace de activación **de un solo uso** y caducidad es un proceso de negocio **distinto** de los enlaces mágicos de recuperación. No se ha implementado aún.
- Se añadió una interfaz inicial en `/app/accesos` para gestionar únicamente roles ordinarios sobre cuentas ya existentes; la invitación, edición completa de usuarios y otorgamiento de roles institucionales/financieros sensibles **siguen pendientes**. Véase [gestión de accesos](gestion-de-accesos.md).
- No hay todavía relaciones de responsables legales y atletas; los permisos a expedientes se modelarán en INC-003.
- El administrador técnico tendrá capacidades operativas controladas; no debe disponer de autorización funcional ordinaria a datos sensibles.

## Pruebas obligatorias

1. El usuario no autenticado que accede a `/app` es redirigido al login.
2. Una cuenta autenticada sin `portal.access` no puede ingresar.
3. No existen rutas GET ni POST de `/register`.
4. Formulario de login incluye campo CSRF.
5. Credenciales incorrectas no inician sesión.
6. La cuenta válida con grupo autorizado puede abrir `/app`.
7. Envío repetido de formularios de login está sujeto al filtro de tasa.
8. UI de login y dashboard funciona en móvil y escritorio.
9. Migraciones Shield y propias se ejecutan con `php spark migrate --all`.
10. Las credenciales y datos reales no están versionados.

## Fuentes de implementación

- https://shield.codeigniter.com/latest/getting_started/install/
- https://shield.codeigniter.com/latest/references/authorization/
- https://shield.codeigniter.com/latest/customization/route_config/
- https://shield.codeigniter.com/latest/references/controller_filters/
