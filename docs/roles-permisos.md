# Seguridad, roles y permisos

## Principios

- Denegación por defecto y mínimo privilegio para roles operativos.
- Cuentas individuales, roles múltiples por persona y alcance por recurso.
- Dennis y fundadores: administración institucional integral según decisión comunicada.
- Una persona designada administra asignaciones de permisos.
- Administrador técnico: soporte y recuperación, no carta blanca para acceder a expedientes o movimientos confidenciales.
- Nadie se eleva a sí mismo a administrador institucional desde el panel.
- Cambios sensibles auditados y con autorización adicional según política.
- Separar permisos `read`, `create`, `update`, `approve`, `publish`, `void`.

## Matriz inicial propuesta

| Perfil | Operación deportiva | Cobros operativos | Fondo de Junta | Edición pública | Gestión de usuarios |
|---|---|---|---|---|---|
| Dennis / fundadores | Integral | Integral | Supervisión autorizada | Aprobar/publicar | Conforme responsable designado |
| Director Deportivo | Según alcance | No | No | Según permiso | No |
| Entrenador | Sus categorías | No | No | Borradores si delegado | No |
| Utilero | Captura autorizada | Registrar cobros | No | Proponer fotos | No |
| Tesorero Junta | No por cargo | No | Administrar caja | Proponer comunicados | No |
| Miembro Junta | Según delegación | No | Según responsabilidad | Proponer contenidos | No |
| Colaborador multimedia | No | No | No | Crear borradores | No |
| Padre/responsable | Solo sus hijos | Consultar sus comprobantes | Transparencia aprobada | No | No |
| Administrador técnico | Soporte limitado | No operativo | No operativo | Soporte limitado | Solo delegado |

Esta matriz es una propuesta funcional sujeta a revisión formal de accesos especialmente sensibles.

## Protección de menores

- No publicar perfil o fotografía identificable sin consentimiento vigente del representante legal.
- Consentimiento específico para perfil individual y para uso editorial, con registro de versión.
- Evitar publicar datos de contacto, escuela, domicilio, horarios personales y otra información sensible.
- Seguimiento disciplinario y de bienestar: registros de acceso restringido, lenguaje objetivo y alternativa si el señalado es el receptor habitual.
- Consentimiento y retiro deben reflejarse en las páginas publicadas.

## Invitaciones y recuperación

Un administrador autorizado vincula al adulto con el atleta y envía un enlace de activación por correo, temporal y de un solo uso. Las solicitudes de recuperación no revelan si existe una cuenta. Aplicar controles de sesión, limitación de intentos y autenticación reforzada a administradores.

## Auditoría

Registrar actor, acción, entidad, fecha, cambios significativos y motivo sin guardar contraseñas ni datos secretos. Accesos del administrador técnico a infraestructura deben administrarse por separado.
