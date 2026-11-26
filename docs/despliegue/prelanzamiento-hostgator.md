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

En esta rama dedicada, **el modo de prelanzamiento es el valor predeterminado tanto en producción como en WAMP**. Para visualizar temporalmente el sitio completo en esta rama se requiere configurar explícitamente `academy.launchMode = full`; para trabajar normalmente sobre el sitio completo, lo recomendado es cambiar a la rama de desarrollo funcional.

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


## Nota de diseño y funcionamiento (26/11/2026)

El diseño se simplificó a un solo impacto visual: **escudo oficial + «Algo grande está por comenzar» + comunidad deportiva + tres valores**. No se muestran fotografías, galerías, menús de navegación ni funcionalidades del sitio final.

Para comprobarlo en WAMP, cambiar a `feature/prelanzamiento-web`, actualizar y abrir `http://localhost:8080/`. Si todavía aparece el sitio completo, revisar que la copia local esté en esa rama, que no exista `academy.launchMode = full` en el archivo `.env` y que el navegador no esté mostrando caché anterior. No es necesario PHP o MySQL en HostGator si se sube solamente la versión estática.


## SEO y vista previa en redes sociales — 26/11/2026

**URL canónica establecida para el prelanzamiento:** `https://academiaramonsanchez.com/`. Confirmar que este dominio sea el principal y que el SSL responda antes de dar por validado el despliegue.

La versión HTML estática (`deploy/prelaunch/index.html`) y la vista CI4 (`app/Views/public/prelaunch.php`) incluyen:
- `lang="es-SV"`, título único y meta description fiel a la identidad.
- `rel="canonical"` absoluto HTTPS; `robots=index,follow,max-image-preview:large`.
- Open Graph completo: `og:type`, `og:locale`, `og:site_name`, `og:url`, título, descripción, imagen absoluta HTTPS, tipo JPEG, dimensiones 1200×630 y texto alternativo.
- `twitter:card=summary_large_image` y título, descripción e imagen para X; las plataformas que interpretan Open Graph pueden reutilizar el mismo arte.
- JSON-LD `SportsOrganization` con nombre institucional, disciplina, ubicación general y logo. No se declaran cuentas sociales, teléfonos, emails, premios o fechas sin confirmación.
- Archivos estáticos `robots.txt` y `sitemap.xml` que enumeran **únicamente la raíz del prelanzamiento**.

**Archivo adicional obligatorio para que aparezca la miniatura en WhatsApp/Facebook:**

```text
assets/img/social/prelanzamiento-ramon-sanchez-v1.jpg
```

Se preparó aparte en el paquete de imagen social adjunto a esta entrega; **no se incluye como binario en la rama GitHub**. Es necesario **subirlo manualmente** al *document root* del dominio, manteniendo exactamente el nombre y la ruta indicados. No reemplazarlo con el SVG porque varias aplicaciones no usan SVG como miniatura OG. El archivo debe responder **200 OK** sin sesión, bloqueo de bots ni redirección a `index.html`, con tipo `image/jpeg`.

### Archivos requeridos en la raíz del dominio (solo micrositio estático)

```text
<document-root>/
├── index.html                          ← deploy/prelaunch/index.html
├── robots.txt                          ← deploy/prelaunch/robots.txt
├── sitemap.xml                         ← deploy/prelaunch/sitemap.xml
└── assets/
    ├── css/prelaunch.css               ← public/assets/css/prelaunch.css
    └── img/
        ├── brand/escudo-oficial.svg     ← public/assets/img/brand/escudo-oficial.svg
        └── social/prelanzamiento-ramon-sanchez-v1.jpg ← paquete de imagen social
```

No subir `app/`, `vendor/`, `.env`, `writable/`, backups ni expediente de menores para esta etapa.

### Checklist técnica de lanzamiento

- [ ] `https://academiaramonsanchez.com/` carga y su código fuente contiene las etiquetas `og:*`, `twitter:*`, canonical y JSON-LD.
- [ ] `https://academiaramonsanchez.com/assets/img/social/prelanzamiento-ramon-sanchez-v1.jpg` responde 200, es JPEG, mide 1200×630 y carga sin inicio de sesión.
- [ ] `https://academiaramonsanchez.com/robots.txt` y `/sitemap.xml` responden 200.
- [ ] HTTPS configurado y versión www/no-www consolidada con redirección 301 a la canónica; configurar esto en cPanel/host si procede.
- [ ] Portada visible en móvil y escritorio; los enlaces internos privados siguen sin estar desplegados.
- [ ] Validar URL mediante [Meta Sharing Debugger](https://developers.facebook.com/tools/debug/); forzar **Scrape Again** cuando la imagen previa está cacheada.
- [ ] Compartir la URL en un chat de WhatsApp de prueba y en Facebook; comprobar imagen, título, descripción y destino. Instagram no garantiza tarjeta de vista previa en todas sus superficies.
- [ ] Revisar la portada con [Google Rich Results Test](https://search.google.com/test/rich-results) o [Schema Validator](https://validator.schema.org/) y verificar datos estructurados. Su presencia no garantiza un resultado enriquecido.
- [ ] Agregar propiedad del dominio y sitemap a Google Search Console solo si se desea indexar la página de expectativa; no subir contenidos internos aún.

**Nota:** etiquetas correctas no obligan a una plataforma a mostrar una imagen. La miniatura final depende del acceso público de los bots, formato/medidas, caché y reglas propias de la red. Tampoco garantizan posiciones SEO.

### Actualizar la vista previa tras lanzar oficialmente
Al sustituir el micrositio, actualizar `og:title`, `og:description`, `og:image`, JSON-LD y sitemap para el contenido real. Usar nombre de archivo nuevo para la imagen OG y actualizar las etiquetas para evitar cachés obsoletas.
