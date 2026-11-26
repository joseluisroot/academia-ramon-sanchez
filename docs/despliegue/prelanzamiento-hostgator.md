# Prelanzamiento de Academia Ramón Sánchez en HostGator

**Fecha:** 26/11/2026. **Estado:** implementación en GitHub lista para verificación, NO desplegada en el hosting. No cambia la ruta de incrementos funcionales ni declara el sitio listo para producción.

## Objetivo
Mostrar en el dominio una página institucional con el **escudo oficial**, valores (disciplina, respeto y compañerismo), expectativas de nueva etapa y presentación de calidad, **sin** publicar todavía el sitio final ni exponer expedientes de menores o el portal privado.

## Vía recomendada: publicar SOLO el micrositio estático (sin PHP ni base de datos)

El desarrollo completo continúa en WAMP y GitHub; no necesitamos desplegar CodeIgniter para mostrar una página de expectativa.

**En el document root asignado al dominio en HostGator (verificar en cPanel > Domains):**

```text
<document-root-del-dominio>/
├── index.html              ← deploy/prelaunch/index.html
└── assets/
    ├── css/
    │   └── prelaunch.css   ← public/assets/css/prelaunch.css
    └── img/
        └── brand/
            └── escudo-oficial.svg ← public/assets/img/brand/escudo-oficial.svg
```

1. En **cPanel > Domains** confirmar qué carpeta es el *document root* del dominio. No asumir que sea `public_html` si el dominio es adicional.
2. Asegurar SSL/TLS activo, DNS apuntando correctamente y que no haya otro `index.php` o `index.html` de un sitio previo que tome prioridad. Respaldar cualquier página existente antes de sustituirla.
3. Subir exactamente los tres archivos indicados por el **Administrador de Archivos** o SFTP. Las carpetas `assets/css` y `assets/img/brand` deben existir. No copiar el repositorio completo ni `app/`, `vendor/`, `.env`, `writable/`, fotos de entrenamiento o backups.
4. Verificar el dominio por HTTPS desde un navegador privado, escritorio y teléfono. Comprobar carga de CSS, imagen del escudo (sin errores 404) y que `/app`, `/login` y cualquier ruta de registro devuelvan 404 (no formularios operativos).
5. Confirmar que no haya listado de directorios y que no se publicaron datos de atletas ni imágenes provisionales de Facebook.
6. Al lanzar oficialmente el sitio, retirar el `index.html` estático, desplegar la aplicación CI4 conforme a su propio procedimiento y hacer QA. Mantener un respaldo del micrositio para revertir.

**Seguridad:** este camino es el recomendado para un hospedaje compartido durante el desarrollo porque no necesita configurar PHP 8.2+, Composer, Shield, base de datos, cron ni permisos de carpetas; ninguno de los módulos internos está en el servidor.

## Alternativa técnica (NO recomendada ahora): CI4 con modo de prelanzamiento

Se añadió la vista `app/Views/public/prelaunch.php`, `app/Services/SiteLaunchMode.php` y un filtro global `app/Filters/PrelaunchGate.php`.

En producción, **el modo de prelanzamiento es el valor predeterminado**, y en WAMP/desarrollo se mantiene el sitio completo salvo configuración contraria.

Variables de ejemplo en `.env`:

```ini
CI_ENVIRONMENT = production
app.baseURL = 'https://DOMINIO-CONFIGURADO/'
academy.launchMode = prelaunch
```

- `academy.launchMode = prelaunch`: `GET /` muestra la página de expectativa; otras rutas GET dinámicas redirigen a `/`; los métodos distintos de GET reciben 404. No se habilita el login.
- `academy.launchMode = full`: al **momento del lanzamiento oficial**, muestra la portada completa y las demás rutas **siempre que** la app ya haya superado sus pruebas y revisiones de seguridad.
- Valores desconocidos o vacíos no activan el sitio completo.

**Advertencia:** incluso con filtro, los archivos públicos son accesibles por URL directa. Las fotografías temporales de Facebook **no deben estar en el servidor público**. No desplegar CI4 completo mientras queden funciones de administración en pruebas, ni colocar `app/`, `vendor/`, `.env`, `writable/`, backups o SQL en el directorio servido por Apache.

Antes de una salida CI4: comprobar soporte PHP **8.2 o superior** (versión mínima validada por `public/index.php` del repositorio actual), dependencias de Composer, SSL, rutas/rewrites, `app.baseURL`, directorio público, CSRF/Shield y `writable/` fuera de acceso web.

## QA de publicación

- [ ] Dominio y SSL verificados en navegador público.
- [ ] Solo `index.html`, CSS y escudo visibles en el *document root*.
- [ ] Hero, mensaje, colores y valores legibles en escritorio y móvil.
- [ ] No se visualiza «sitio en construcción» ni fecha ficticia de lanzamiento.
- [ ] Ninguna imagen de menores publicada.
- [ ] `/app` y `/login` no exponen funcionalidades administrativas.
- [ ] Probados 404 de enlaces inexistentes y ausencia de listado de carpetas.
- [ ] Plan de reversión y archivos anteriores respaldados.
- [ ] Aprobación visual institucional antes de anunciar el dominio.

## Alcance de este cambio
Un corte aislado de prelanzamiento, sin migraciones ni cambios en los datos de la academia; INC-004 sigue abierto y su QA pendiente.
