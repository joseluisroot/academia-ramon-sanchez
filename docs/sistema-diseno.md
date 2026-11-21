# Sistema de diseño UI/UX

## Dirección visual

Institucional, **elegante, profesional, moderna y sobria**. Una sola línea gráfica para el sitio público y el portal privado, con énfasis distinto: emocional/editorial en público y eficacia operativa en administración.

## Tokens iniciales

| Token | Valor | Uso |
|---|---|---|
| Navy | `#071B49` | encabezados y superficies principales |
| Institutional | `#062B83` | identidad institucional |
| Action | `#1165B5` | botones y enlaces principales |
| Background | `#F5F7FB` | fondos claros |
| Text | `#172033` | texto principal |
| White | `#FFFFFF` | tarjetas y contraste |

Los tokens de estados (éxito, aviso, error), focos, bordes, tipografía y escala de espacios serán definidos en componentes reutilizables y comprobados con criterios de accesibilidad.

## Principios

1. Diseño primero para teléfono, adaptable a escritorio.
2. Navegación coherente entre módulos y breadcrumbs cuando correspondan.
3. Formularios con etiquetas explícitas, validación junto al campo y errores entendibles.
4. Acciones peligrosas con confirmación y trazabilidad.
5. Estados vacíos, carga, éxito y error diseñados; no solo la pantalla ideal.
6. Tablas utilizables en móvil; filtros visibles para roles operativos.
7. Contraste, foco, navegación por teclado y objetivos táctiles adecuados.
8. Fotografías reales autorizadas; no usar imágenes de menores sin consentimiento.
9. Información privada separada de la presentación pública.
10. Componentes de formularios, botones, tarjetas, diálogos, tablas, mensajes y badges documentados.

## Layouts

### Sitio público
Barra de navegación institucional, portada de valor y llamada a clase gratuita, secciones deportivas y sociales, pie de página con contacto y enlaces de política.

### Portal privado
Barra superior, navegación contextual según rol, panel de tareas priorizadas, búsqueda, formularios y reportes. Acciones principales accesibles con pocas interacciones.

## Prototipos funcionales de referencia

- Ficha del atleta: foto autorizada, categoría, dorsal, datos privados limitados por rol.
- Perfil público: palmarés, goles, asistencias e insignias aprobadas.
- Calendario: distinguir preliminar, confirmado, reprogramado y cancelado.
- Convocatorias: selección por categoría y confirmación familiar.
- Talonario: períodos individuales, fecha de vencimiento, gracia, mora, PDF de comprobante.
- Uniformes: selección local/entrenamiento, dorsal, talla, pago, fabricación y entrega.
- Tesorería: caja común, ingresos consolidados, gastos y transparencia agregada.
- Editor multimedia: borrador, revisión de Dennis y publicación.

## Definition of Done UI

Se considera terminado cuando cumple las vistas móvil/escritorio, estados y mensajes, permisos, manejo de error, accesibilidad básica y consistencia con tokens aprobados.


## Identidad oficial — actualización INC-001A

**Fuente:** escudo oficial proporcionado por la academia (incluye el nombre completo y año 2023). Su uso prevalece frente a cualquier monograma o logo genérico.

**Recurso en el repositorio:** `public/assets/img/brand/escudo-oficial.svg`. El SVG contiene una copia WebP compacta de la imagen entregada, optimizada para espacios pequeños. No es una vectorización del escudo ni sustituye el archivo maestro original.

### Ubicaciones implementadas

- Encabezado del sitio público, junto a nombre institucional.
- Pie de página público.
- Pantalla de inicio de sesión.
- Encabezado del portal privado.
- Ícono del sitio (favicon SVG).

### Reglas de uso

1. No deformar, girar, recolorear ni alterar elementos del escudo.
2. Mantener proporción cuadrada con `object-fit:contain`.
3. En fondos oscuros utilizar superficie blanca detrás del emblema para conservar contraste.
4. Conservar texto alternativo descriptivo cuando el escudo comunica identidad.
5. Evitar escalar el derivado WebP compacto para portadas, impresos o documentos de gran formato: usar la imagen maestra de mayor resolución.
6. No reemplazar el nombre institucional por el emblema en pantallas móviles; mostrar ambos cuando quepan sin pérdida de legibilidad.
7. Probar lectura y tamaño mínimo en móvil, especialmente por la cantidad de texto dentro del escudo.

### Mejoras previstas

- Preparar un archivo PNG/WebP de alta resolución y fondo transparente si existe una versión aprobada.
- Generar variantes favicon de 16/32/48 píxeles con símbolo simplificado **solo después de autorización institucional**.
- Definir una versión horizontal oficial, si los fundadores disponen de ella.

> El recurso actual se creó con la imagen oficial facilitada durante el análisis. No se han alterado nombres ni elementos del escudo. El archivo original en mayor resolución debe conservarse fuera de las variantes optimizadas para web.
