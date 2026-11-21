# Instalación local — INC-001

**Estado:** primera estructura CI4 y Shield incorporadas; instalación de dependencias, migraciones y ejecución **pendientes de validar en WAMP**.

## Requisitos mínimos

- PHP **8.2 o superior** (se recomienda versión actual compatible con CodeIgniter 4.7).
- Composer 2.
- Extensiones PHP `intl` y `mbstring`; además `mysqli` para MySQL.
- WAMPServer en Windows y MySQL disponible.
- Git instalado.

## 1. Actualizar la rama

En PowerShell:

```powershell
cd C:\wamp64\www\acedemia-ramon-sanchez
git status
git switch feature/inc-001-fundamentos
git pull --ff-only origin feature/inc-001-fundamentos
```

No eliminar ni descartar modificaciones locales si `git status` indica cambios.

## 2. Instalar dependencias

```powershell
php -v
composer --version
composer install
# Si ya existe composer.lock local anterior sin Shield, en su lugar ejecutar:
# composer update codeigniter4/shield --with-all-dependencies
# Conservar composer.lock en la rama cuando esté validado
```

**Nota:** el repositorio todavía no incluye `composer.lock`, por lo que Composer resolverá dependencias compatibles. Guardar el archivo `composer.lock` generado en la rama para fijar versiones de instalación.

## 3. Crear variables locales

```powershell
Copy-Item env .env
```

Editar `.env` **sin subirlo a Git**:

```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'

database.default.hostname = localhost
database.default.database = academia_ramon_sanchez
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```

Las credenciales son **ejemplo de desarrollo**; no se utilizarán en producción. Crear previamente la base de datos local desde una cuenta autorizada.

## 4. Ejecutar

```powershell
php spark serve --host localhost --port 8080
```

Abrir `http://localhost:8080/`. Debe aparecer la portada institucional inicial.

También se puede configurar un VirtualHost de Apache que apunte a `C:\wamp64\www\acedemia-ramon-sanchez\public`. **Nunca apuntar el DocumentRoot al directorio raíz del proyecto**.

## 5. Verificaciones

```powershell
php spark routes
php spark migrate --all
php spark migrate:status
# No ejecutar shield:setup: ya existen los archivos Auth y AuthGroups configurados.

# Para el primer administrador institucional en entorno local:
php spark shield:user create -n usuario_institucional -e persona@example.invalid -g institutional_admin
# Designar únicamente al responsable autorizado de permisos:
php spark shield:user addgroup -n usuario_institucional -g access_manager
```

Se espera encontrar la ruta pública `/`. Todavía **no hay inicio de sesión, tablas de usuarios ni dashboard privado funcional**: se implementarán y probarán en los próximos cambios de este mismo incremento.

## Seguridad

- No usar datos reales de menores en desarrollo.
- No publicar `.env`, `vendor/`, `writable/` ni respaldos.
- La ruta `/app` no debe exponerse hasta que exista autenticación y autorización probadas.
- No publicar la plataforma en hosting antes de cerrar las pruebas y seguridad del INC-001.

## Próximas actividades del incremento

1. Instalar y verificar CodeIgniter Shield y dependencias en WAMP.
2. Validar permisos institucionales en sesión/rutas y desarrollar interfaz controlada de asignación.
3. Verificar migraciones propias de personas y bitácora; construir invitaciones seguras y su auditoría.
4. Validar layout privado, pruebas y sistema visual; incorporar Tailwind sin duplicación de estilos.
5. Validar Composer, framework, MySQL y responsive en entorno real.

## URLs limpias: sin `index.php`

Desde INC-001, `app/Config/App.php` define `$indexPage = ''` para generar enlaces como:

- `http://localhost:8080/login`
- `http://localhost:8080/app`

El servidor embebido iniciado mediante `php spark serve` resuelve las rutas de CodeIgniter.

Para usar WAMP/Apache, el VirtualHost debe apuntar a la carpeta `public/`, con `mod_rewrite` habilitado y las reglas `public/.htaccess` activas. Si una página falla con 404 al eliminar `index.php`, revisar `AllowOverride All`, `mod_rewrite` y el DocumentRoot antes de cambiar rutas.

Si el archivo local `.env` establece `app.indexPage = 'index.php'`, eliminar ese valor o establecer `app.indexPage = ''` para que no sobreescriba la configuración versionada.

Tras ejecutar `git pull --ff-only`, reiniciar `php spark serve` y comprobar `/login`, `/app` y que el formulario de inicio de sesión genere una acción sin `index.php`.

Nota: esta configuración no habilita registros públicos ni modifica los filtros de seguridad.
