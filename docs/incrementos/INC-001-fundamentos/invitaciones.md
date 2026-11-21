# INC-001 — Invitaciones individuales

**Estado:** implementación inicial en rama, **pendiente de ejecutar y validar en WAMP**.

## Propósito

Solo un responsable designado podrá invitar personas a la plataforma. La academia **no habilita el registro público**. Cada usuario invitado define su propia contraseña mediante un enlace individual.

## Componentes incorporados

- Migración `user_invitations`: email, grupo solicitado, hash del token, estado, responsable, creación, vencimiento y consumo.
- Modelo `UserInvitationModel`.
- Política `InvitationPolicy`.
- Servicio `UserInvitationService`.
- Panel `GET/POST /app/invitaciones` exclusivo de `users.invite`.
- Formulario de activación `GET/POST /activar-cuenta/{token}`.
- CSS institucional responsive, auditoría y pruebas unitarias de reglas puras.

## Flujo funcional

```mermaid
flowchart TD
    A[Responsable de accesos] --> B[Ingresa email y rol ordinario]
    B --> C{¿SMTP listo y no hay cuenta previa?}
    C -->|No| X[No generar invitación vigente]
    C -->|Sí| D[Generar token aleatorio de 32 bytes]
    D --> E[Guardar SHA-256 del token]
    E --> F[Enviar correo con enlace temporal de 48 horas]
    F --> G[Invitado abre enlace]
    G --> H{¿Token vigente y sin usar?}
    H -->|No| I[Enlace inválido o vencido]
    H -->|Sí| J[Elige usuario y contraseña]
    J --> K[Crear cuenta Shield y asignar grupo]
    K --> L[Marcar invitación consumida y auditar]
    L --> M[Redirigir al login]
```

## Restricciones

- Roles invitables: `guardian`, `coach`, `media_contributor`. Roles de tesorería, equipo institucional y soporte requieren autorización distinta.
- Enlace con token criptográficamente aleatorio, almacenado **solo como hash SHA-256**.
- Vigencia inicial: 48 horas. No reutilizable: consumo condicionado por estado y vencimiento en la base.
- No enviar contraseñas ni almacenar el token en texto plano en la base ni la bitácora.
- Se rechaza nueva invitación vigente a un correo ya invitado o con cuenta existente.
- Contraseña: mínimo 12 caracteres, mayúscula, minúscula, número y símbolo; no contiene el nombre de usuario.
- El servidor exige sesión y permiso para crear invitaciones; solo el formulario público de activación acepta el token.
- El formulario público debe llevar CSRF y políticas de caché/referrer restrictivas.
- No se enlaza automáticamente el padre con un expediente deportivo. Eso se realizará mediante verificación administrativa en INC-003.

## Configuración necesaria para correo

El servidor necesita un SMTP confiable y configuración local de `app.baseURL` pública **accesible para el invitado**. Un enlace a `localhost` enviado a otra computadora no será funcional.

Definir únicamente en `.env` (valores de ejemplo; NO subir secretos):

```ini
email.protocol = smtp
email.SMTPHost = smtp.example.invalid
email.SMTPUser = usuario_smtp
email.SMTPPass = CONTRASENA_SOLO_LOCAL
email.SMTPPort = 587
email.SMTPCrypto = tls
email.fromEmail = invitaciones@example.invalid
email.fromName = 'Academia Ramón Sánchez'
```

**No activar envíos reales hasta validar proveedor, dominio y configuración SMTP.** En desarrollo utilizar un servidor de pruebas de correo o buzón simulado. La funcionalidad informa error cuando SMTP no está configurado.

**Nota operativa:** el correo se envía antes del commit del registro de invitación. Aunque se verifican fallos de envío, el correo externo no participa en la transacción de MySQL; si hubiera un fallo inesperado al confirmar, el enlace podría quedar inválido. Para producción conviene incorporar un patrón outbox/cola con estados de envío y reintentos auditados.

## Pruebas de aceptación pendientes

- [ ] Las rutas `/register` continúan deshabilitadas.
- [ ] Solo `users.invite` puede acceder al panel y enviar invitaciones.
- [ ] Sin SMTP configurado no se confirma un envío.
- [ ] Enviar un correo real únicamente al buzón de prueba autorizado.
- [ ] El enlace vence después de 48 horas.
- [ ] No permite volver a utilizar el mismo enlace.
- [ ] La segunda invitación vigente al mismo correo se rechaza.
- [ ] Solo se crean roles ordinarios autorizados.
- [ ] Se rechaza nombre de usuario duplicado o inválido.
- [ ] El alta Shield y el cambio de estado se confirman en la misma transacción.
- [ ] El proceso no muestra contraseña ni token en logs.
- [ ] Auditoría de invitación y activación persistida.
- [ ] Revisión visual móvil y escritorio.
- [ ] Pruebas unitarias: `vendor\\bin\\phpunit --bootstrap vendor/autoload.php tests/unit/InvitationPolicyTest.php`.

## Pendientes para un uso definitivo

- Reenvío/cancelación de invitaciones por responsable autorizado.
- Cola de envío por SMTP con trazabilidad de entrega y reintentos.
- Plantilla de correo institucional con escudo.
- Relación administrativa previa entre padres y atletas.
- Verificación adicional para cuentas institucionales.
- Pruebas de carga y limitación de intentos.
