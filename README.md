# Academia de Fútbol Ramón Sánchez

Proyecto integral de **sitio web público** y **portal privado** de gestión de la Academia Ramón Sánchez.

## Estado actual

- Rama en desarrollo: `feature/inc-001-fundamentos`.
- Base oficial **CodeIgniter 4 App Starter** incorporada (framework instalado mediante Composer).
- Primera portada institucional responsive en español: `app/Views/public/home.php`.
- Estilos y tokens iniciales: `public/assets/css/academia.css` (CSS base provisional; integración Tailwind pendiente).
- Arquitectura, reglas y roadmap documentados en `docs/`.
- **Aún no implementados:** login, roles, permisos, migraciones de identidad, portal privado y pruebas integrales. No desplegar en producción.

## Instalación y pruebas locales

Leer [Instrucciones WAMP](docs/incrementos/INC-001-fundamentos/instalacion-local.md) y [documentación maestra](docs/README.md).

```powershell
composer install
Copy-Item env .env
# Configurar .env y MySQL según docs
php spark serve --host localhost --port 8080
```

Luego abrir http://localhost:8080/.

## Arquitectura objetivo

PHP 8.2+ / CodeIgniter 4.7, MySQL, Tailwind CSS (siguiente fase), HTML5 y JavaScript; GitHub con rama y PR por incremento.

## Seguridad

No subir `.env`, credenciales, archivos del directorio `writable` ni información real de menores. El directorio público del servidor es **`public/`**, no la raíz del repositorio.
