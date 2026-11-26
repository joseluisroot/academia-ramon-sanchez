# Revisión integral de UI/UX — Academia Ramón Sánchez
**Inicio:** 22/11/2026. **Alcance revisado:** portal privado y sitio público existentes en la rama `feature/inc-004-entrenamientos-asistencia`.  
**Naturaleza del trabajo:** auditoría del código y primera corrección transversal; **no sustituye QA en navegador, revisión jurídica ni pruebas de usuarios reales**.

## Visión de producto

Una plataforma deportiva institucional, contemporánea y minimalista que inspire confianza desde la primera visita, con imágenes auténticas de la academia, tipografía y componentes consistentes, navegación clara, rendimiento, accesibilidad y permisos estrictos. Evitar el aspecto de plantilla administrativa genérica, estadísticas de relleno, iconografía emoji y animaciones innecesarias.

## Hallazgos

| ID | Hallazgo | Severidad | Acción |
|---|---|---|---|
| UI-01 | Tailwind contemplado en arquitectura, sin configuración ni proceso de build | Alta | Se incorporan archivos de Tailwind CSS v4 CLI y carga opcional de CSS compilado; adopción gradual, sin Preflight que rompa estilos heredados |
| UI-02 | Portada con balón emoji/órbitas sin material fotográfico real | Alta | Hero editorial con escudo institucional como fallback y fotografía autorizada como primer plano cuando corresponda |
| UI-03 | No existe galería de la actividad deportiva | Alta | Grilla editorial y catálogo con control individual de aprobación, archivos webp optimizados preparados por separado |
| UI-04 | Navegación móvil pública ocultaba enlaces clave | Alta | Menú `details/summary` adaptado a móvil, con rutas reales a contacto, academia, categorías y login |
| UI-05 | Estilos distribuidos en archivos extensos con componentes repetidos | Media | Capa `experience.css` para pulir portal sin reescribir todas las vistas; tokens de diseño y preparación de Tailwind. Refactor por componentes pendiente |
| UI-06 | Dashboard y navbar privados inicialmente básicos | Alta | Primeras versiones ejecutivas por permisos, menú institucional con secciones e indicadores contextuales ya implementadas; nuevos refinamientos compartidos |
| UI-07 | Tablas/formularios requieren jerarquía, foco, legibilidad y estados consistentes | Media | Revisión global de bordes, sombras, tamaños, foco, scroll horizontal, hover y espaciado |
| UI-08 | Publicación de menores y derechos del fotógrafo | Crítica | Deshabilitada por defecto; per-photo flag y bandera global. No colocar medios no autorizados en `public/`, aunque estén ocultos |
| UI-09 | No se han ejecutado pruebas completas en dispositivos y usuarios | Alta | Matriz de QA pendiente en WAMP, no declarar implementación aprobada en producción |
| UI-10 | Métricas mostradas deben respetar roles y categorías | Alta | Resúmenes del entrenador limitados a categorías; la navegación visual no reemplaza permisos de servidor |

## Sistema visual

- **Identidad:** escudo oficial, azul marino y azul institucional, superficies suaves, contrastes y acentos discretos. Fotografías reales aprobadas; sin contenido genérico ni estadísticas inventadas.
- **Tipografía:** Inter como fuente preferida cuando esté disponible; fallback sistema. Escala homogénea y textos claros.
- **Componentes:** navbar, cards, formularios, tablas, CTA, estados de error/vacío, hero editorial, galería y navegación responsive.
- **Accesibilidad:** alt descriptivos, teclado/foco visible, `summary` nativo, etiquetas de formulario, tablas accesibles, reducción de movimiento.
- **Privacidad:** imágenes de atletas con consentimiento individual y derechos de fotografía; nunca asumir autorización por haber recibido archivos.

## Integración de Tailwind CSS

Instalación progresiva con CLI v4 (no CDN de prueba) y sin reinicializar todos los estilos de golpe:

```powershell
npm install
npm run css:build
```

El resultado local `public/assets/css/tailwind.css` se carga si existe. La app sigue funcionando sin este archivo mientras se prepara el entorno. `npm run css:dev` compila en modo watch. El generado y `node_modules` se ignoran en Git; en producción hay que compilarlo como parte del despliegue.

**Importante:** el sistema ya tiene la base de Tailwind, pero los estilos heredados aún no están migrados. Las nuevas pantallas adoptarán utilidades y componentes de forma controlada, sin afirmar que todo el portal ya se construyó con Tailwind.

## Plan editorial de fotografías

Selección de **6 fotos** de las aportadas por el usuario. Se entregan optimizadas en un ZIP de revisión, no en el repositorio. Se preservan marcas de agua (CF / Chele Fotógrafo). Propuesta:

| Activo WebP | Destino | Criterio |
|---|---|---|
| `equipo-formacion.webp` | Hero si se publica, primera galería | Retrato grupal transmite institución, trabajo de equipo y entrenador |
| `juego-equipo.webp` | Galería | Acción deportiva y juego colectivo |
| `accion-partido.webp` | Galería | Movimiento y energía de partido |
| `entrenamiento-tecnico.webp` | Galería | Labor formativa y metodología |
| `portero-formacion.webp` | Galería | Visibilidad de diferentes posiciones |
| `valores-equipo.webp` | Galería | Desarrollo infantil y práctica deportiva |

**Proceso seguro de aprobación:**
1. Mantener los WebP en revisión privada, **fuera** de `public/` y de GitHub público.
2. Confirmar autorización de publicación web para **cada niño reconocible** de cada fotografía, y licencia/atribución de Chele Fotógrafo. La documentación debe custodiarse de forma privada, nunca en repositorio.
3. Copiar únicamente imágenes autorizadas a `public/assets/img/gallery/`.
4. Marcar `approved => true` solo para cada imagen autorizada y `approvedForPublication = true` en `app/Config/PublicGallery.php`.
5. Validar encuadre, derecho de publicación, marca de agua, alt, accesibilidad, tiempos de carga y la web pública antes de desplegar.

La bandera por sí sola **no oculta** una imagen que ya se copió al webroot: la URL directa puede abrirla. Por eso está prohibido copiar a `public/` imágenes sin permiso. No se aplica facial blur ni recorte de marcas de agua por defecto.

## Pruebas de aceptación pendientes

- [ ] Public UI: hero en escritorio y móvil con fallback de escudo cuando no hay fotos autorizadas; sin links rotos.
- [ ] Menú público en 360–390px, tablet, desktop; navegación por teclado y CTA/contacto funcionales.
- [ ] Galería se omite cuando no hay imágenes aprobadas; habilitación individual respeta catálogo y evita imágenes inexistentes.
- [ ] Imágenes optimizadas con alt, tamaños, `loading=lazy`, compresión y créditos; validar con autorización.
- [ ] Dashboard por perfil: administrador, director deportivo, entrenador U7/U9, padre, utilero y Junta.
- [ ] Acceso directo por URL sigue protegido en backend; no se confía en la ocultación de menús.
- [ ] Accesibilidad: foco, contraste AA, lector de pantalla, tabulación, zoom 200% y movimiento reducido.
- [ ] Rendimiento: Lighthouse móvil y escritorio, LCP/CLS, caché de imágenes, peso del CSS compilado.
- [ ] Revisar CSS antiguo y eliminar selectores obsoletos solo tras verificar regresión visual de cada pantalla.
- [ ] Revisar con fundadores y dirección deportiva la presentación pública y el dashboard antes de aprobar el PR.

## Límites de este corte

No se han probado en un navegador real las pantallas de esta rama ni se ha publicado ninguna fotografía del usuario en el repositorio. La migración completa de componentes a Tailwind, un sidebar colapsable avanzado, las notificaciones y el comportamiento AJAX permanecen en el backlog de UX y en incrementos posteriores. El PR #4 continúa en borrador.
