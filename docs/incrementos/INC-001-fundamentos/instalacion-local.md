# Instalación local — INC-001

**Estado:** primera estructura CI4 incorporada; dependencias y ejecución **pendientes de validar en WAMP**.

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
php spark migrate:status
```

Se espera encontrar la ruta pública `/`. Todavía **no hay inicio de sesión, tablas de usuarios ni dashboard privado funcional**: se implementarán y probarán en los próximos cambios de este mismo incremento.

## Seguridad

- No usar datos reales de menores en desarrollo.
- No publicar `.env`, `vendor/`, `writable/` ni respaldos.
- La ruta `/app` no debe exponerse hasta que exista autenticación y autorización probadas.
- No publicar la plataforma en hosting antes de cerrar las pruebas y seguridad del INC-001.

## Próximas actividades del incremento

1. Seleccionar e instalar integración oficial de autenticación (se propone CodeIgniter Shield).
2. Implementar permisos institucionales y asignación centralizada.
3. Crear migraciones propias de personas, roles de dominio, invitaciones y bitácora según diseño aprobado.
4. Incorporar layout privado protegido, pruebas y un sistema de componentes visuales compartidos.
5. Validar Composer, framework, MySQL y responsive en entorno real.
