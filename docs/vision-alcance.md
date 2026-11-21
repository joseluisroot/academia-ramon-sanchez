# Visión, alcance y procesos

## Propósito

Construir un ecosistema digital único de la **Academia de Fútbol Ramón Sánchez**, combinando una web institucional pública con un portal privado, con una estética moderna, elegante y móvil.

## Actores

- **Fundadores y Dennis Sánchez**: máxima administración institucional (confirmado como decisión de alcance).
- **Dirección Deportiva**: coordinación técnico-deportiva.
- **Entrenadores**: entrenamiento, asistencia, convocatorias y estadísticas según categoría y permiso.
- **Utilero**: apoyo a inscripción, pedidos de uniformes y cobros ordinarios autorizados.
- **Junta Directiva**: comité de apoyo, organización de actividades y fondo común diferenciado.
- **Padres / responsables legales**: datos de hijos, confirmación de convocatoria, comprobantes y comunicados.
- **Colaborador audiovisual**: preparación de material sin publicación automática.
- **Administrador técnico**: soporte e infraestructura, con acceso controlado.

## Mapa de procesos

| Proceso | Situación actual conocida | Propuesta |
|---|---|---|
| Captación | Recomendaciones y visitas | Web y solicitudes de primera clase gratuita; Dennis recibe y orienta |
| Inscripción | Personal autorizado registra a niño | Expediente único y validación doble |
| Entrenamientos | Miércoles y jueves 16:30–18:00; viernes eventual | Sesiones, asistencia y alertas de seguimiento |
| Competición | Programación general con confirmación semanal | Jornadas, partidos por categorías, cambios y convocatorias |
| Estadísticas | Dennis concentra la información | Goles, asistencias, minutos, resultados, tablas e insignias |
| Cobros ordinarios | Talonario físico con taco y parte para familia | Mensualidades individuales, morosidad y comprobantes digitales |
| Uniformes | Pago, fabricación y entrega aproximada en 15 días | Pedidos de local y entrenamiento a $15 cada uno |
| Fondo de Junta | Caja metálica y libro físico | Caja digital, arqueo, avances, reintegros e informe privado mensual |
| Comunicación | Dennis informa por WhatsApp, grupos separados por categoría | Comunicados oficiales en portal, difusión por grupos/comunidad propuesta |
| Publicaciones | Dennis revisa y decide publicación en Facebook | Biblioteca multimedia, revisión y consentimiento |

## Flujos fundamentales

### Captación a inscripción

```mermaid
flowchart TD
    A[Familia interesada] --> B[Dennis recibe y explica]
    B --> C[Primera clase de prueba gratuita]
    C --> D{¿Desea continuar?}
    D -->|No| E[Cierre sin expediente definitivo]
    D -->|Sí| F[Captura administrativa]
    F --> G[Validación de atleta y responsables]
    G --> H[Asignación de dorsal]
    H --> I[Matrícula / mensualidad y pedido opcional de uniforme]
    I --> J[Expediente activo]
```

### Jornada deportiva

```mermaid
flowchart TD
    A[Calendario preliminar] --> B[Delegado habitual o sustituto asiste a reunión]
    B --> C[Registra cambios de programación]
    C --> D[Dennis o Dirección Deportiva verifica]
    D --> E[Convocatorias por categoría]
    E --> F[Familias confirman]
    F --> G[Partido]
    G --> H[Registro de resultado y eventos]
    H --> I[Verificación]
    I --> J[Clasificación, goleadores y perfiles autorizados]
```

### Cobro y comprobante

```mermaid
flowchart TD
    A[Ingreso del atleta y fecha de referencia] --> B[Generación de mensualidades individuales]
    B --> C{¿Pago completo recibido?}
    C -->|No| D[Pendiente / gracia / mora según política]
    C -->|Sí| E[Registro por utilero o fundador autorizado]
    E --> F[Comprobante único numerado]
    F --> G[Talonario digital visible a familia autorizada]
```

## Alcance público

Historia institucional, presentación del cuerpo técnico, categorías U7/U9/U11/U13/U17, horarios y contacto, solicitud de clase gratuita, noticias y galería autorizada, torneos, resultados, clasificación, goleadores, perfiles deportivos e insignias con consentimiento.

## Alcance privado

Gestión de usuarios y permisos, expedientes y responsables, entrenamientos/asistencia, jornadas/convocatorias, estadísticas, matrícula/mensualidades/talonarios, uniformes, fondo de Junta, transparencia familiar, comunicaciones y contenido editorial.

## Beneficios

- Continuidad operativa y menor dependencia de registros dispersos.
- Información confiable y reutilizable, sin duplicidad de captura.
- Transparencia del fondo de Junta **sin mezclarlo con los ingresos de fundadores**.
- Mejor comunicación y participación de familias.
- Identidad pública profesional y promoción orgánica.
- Historial formativo y deportivo del atleta.
