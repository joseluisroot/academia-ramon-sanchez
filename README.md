# Academia de Fútbol Ramón Sánchez

Proyecto integral de **sitio web público** y **portal privado** de gestión de la Academia Ramón Sánchez.

## Estado actual

- Rama de desarrollo vigente: `feature/inc-004-entrenamientos-asistencia` (PR #4 en borrador, encadenado a INC-001–INC-003).
- Base oficial **CodeIgniter 4 App Starter** incorporada (framework instalado mediante Composer).
- Primera portada institucional responsive en español: `app/Views/public/home.php`.
- Sistema visual en evolución: `public/assets/css/academia.css`, `editorial.css`, `portal.css` y `experience.css`; primera integración progresiva de Tailwind v4 CLI preparada en `resources/css/tailwind.css`.
- Arquitectura, reglas y roadmap documentados en `docs/`.
- **En la rama, sin validar en WAMP:** configuración CodeIgniter Shield, login institucional, catálogo de roles/permisos, rutas protegidas `/app`, migraciones `persons` y `audit_logs`, modelos y primer panel privado.
- **Pendiente:** QA integral, revisión visual móvil, publicación condicionada de fotografías y cierre formal de incrementos. No desplegar en producción.

## Instalación y pruebas locales

Leer [Instrucciones WAMP](docs/incrementos/INC-001-fundamentos/instalacion-local.md) y [documentación maestra](docs/README.md).

```powershell
composer install
Copy-Item env .env
# Configurar .env y MySQL según docs
php spark migrate --all
php spark serve --host localhost --port 8080
```

Luego abrir http://localhost:8080/.

## Arquitectura objetivo

PHP 8.2+ / CodeIgniter 4.7, MySQL, Tailwind CSS (siguiente fase), HTML5 y JavaScript; GitHub con rama y PR por incremento.

## Seguridad

No subir `.env`, credenciales, archivos del directorio `writable` ni información real de menores. El directorio público del servidor es **`public/`**, no la raíz del repositorio.

## Interfaz visual y galería

El sitio público incorpora una nueva composición editorial y galería preparada para imágenes previamente autorizadas; por defecto **no publica fotografías de menores**. Consultar `docs/diseno/REVISION-UI-UX-2026-11-22.md` para selección de fotografías, autorización de imagen, licencia del fotógrafo y carga segura de medios.

En el portal, el dashboard ejecutivo, navbar y los estilos comunes se adaptan a los permisos del usuario. La ocultación de enlaces no sustituye la autorización del backend.

### Tailwind CSS v4 (opcional durante transición)

```powershell
npm install
npm run css:build
# Para desarrollo continuo:
npm run css:dev
```

Los layouts cargan el CSS compilado localmente si existe; los estilos clásicos funcionan sin compilar Tailwind mientras migramos. No usar el CDN Play en producción.
