# INC-001 — Control de pruebas y evidencias

**Estado:** verificación funcional parcial en WAMP, reportada por el usuario el 21 de noviembre de 2026. Los elementos marcados como pendientes requieren una ejecución y evidencia independiente; no suponer que se aprobaron por existir código.

## Leyenda

- [x] Confirmada manualmente por el usuario.
- [ ] No ejecutada o no confirmada.
- **Evidencia:** registrar fecha, ambiente, usuario/rol de prueba (sin contraseña), resultado y, cuando proceda, captura sin datos personales.

## Pruebas confirmadas en WAMP

- [x] La página pública carga y presenta el escudo institucional.
- [x] El sitio genera URLs sin `index.php`.
- [x] Cuenta administrativa `joseluisroot`: inicio de sesión exitoso.
- [x] Cuenta administrativa: acceso a `/app`.
- [x] Cuenta con `access_manager`: acceso a `/app/accesos`.
- [x] Cuenta con `access_manager`: acceso a `/app/invitaciones`.
- [x] Recepción del correo de invitación en Mailpit.
- [x] Apertura del enlace de activación.
- [x] Creación de cuenta de prueba mediante activación.
- [x] La invitación aparece con estado **Aceptada**.
- [x] La cuenta invitada `guardian` puede acceder a `/app`.
- [x] La cuenta `guardian` no puede acceder a `/app/accesos`.
- [x] La cuenta `guardian` no puede acceder a `/app/invitaciones`.

**Aclaración UI:** el historial mostraba «Sin acciones» para invitaciones aceptadas: significa que **no hay operaciones disponibles**, no que el usuario carezca de permisos. La etiqueta se mejora en INC-001B.

## Invitaciones — casos pendientes

- [ ] **INV-01** Reutilizar un enlace ya aceptado: debe rechazarse.
- [ ] **INV-02** Cancelar una invitación pendiente: el enlace debe quedar inválido.
- [ ] **INV-03** Invitar nuevamente tras cancelar: se crea otro token y vence el anterior.
- [ ] **INV-04** Vencimiento temporal a las 48 horas: enlace rechazado.
- [ ] **INV-05** Segunda invitación vigente para el mismo correo: rechazada.
- [ ] **INV-06** Correo ya registrado como usuario: alta duplicada rechazada.
- [ ] **INV-07** SMTP no configurado o desconectado: error claro sin falsa confirmación.
- [ ] **INV-08** Nombre de usuario inválido, duplicado o con guion bajo: rechazado.
- [ ] **INV-09** Contraseña débil o confirmación distinta: rechazada.
- [ ] **INV-10** Dos intentos concurrentes de consumir un token: una única cuenta creada.
- [ ] **INV-11** Rol sensible inyectado por POST: rechazado.
- [ ] **INV-12** Límite de 5 invitaciones por hora: bloquea exceso.
- [ ] **INV-13** Auditoría de invitación, cancelación y aceptación: sin tokens ni contraseñas.
- [ ] **INV-14** Correo de prueba no genera enlaces accesibles fuera de localhost.
- [ ] **INV-15** CSRF en formularios de emisión, cancelación y activación: rechaza POST inválido.

## Autenticación y autorización — casos pendientes

- [ ] **AUT-01** Visitante sin sesión en `/app`: enviado a inicio de sesión.
- [ ] **AUT-02** Usuario `guardian` en `/app/accesos`: ve pantalla institucional 403; nunca obtiene datos ni acceso.
- [ ] **AUT-03** Usuario `guardian` en `/app/invitaciones`: misma restricción.
- [ ] **AUT-04** Usuario administrador en rutas permitidas: no recibe bloqueo indebido.
- [ ] **AUT-05** Logout: sesión anterior no puede reutilizarse.
- [ ] **AUT-06** Cuenta sin `portal.access`: acceso a `/app` rechazado.
- [ ] **AUT-07** Cambios de grupo: sin autoasignación y sin cambios en cuentas privilegiadas.
- [ ] **AUT-08** Tokens, cookies, credenciales y datos personales no se muestran en errores o bitácoras.

## Páginas institucionales y UX/UI — casos pendientes

- [ ] **UI-01** `/sin-acceso` muestra SVG de acceso restringido y responde con HTTP 403.
- [ ] **UI-02** Ruta inexistente muestra SVG 404 y responde HTTP 404.
- [ ] **UI-03** La página 404 funciona sin sesión iniciada.
- [ ] **UI-04** Las pantallas tienen escudo, textos legibles, enlaces correctos y adaptación móvil.
- [ ] **UI-05** No aparecen errores PHP, información sensible o rutas internas en el HTML.
- [ ] **UI-06** Se distingue «Invitación aceptada · sin operaciones disponibles» de falta de permisos.
- [ ] **UI-07** Los enlaces de regresar al panel solo se ofrecen a usuarios autenticados.
- [ ] **UI-08** La página 403 no genera un bucle al visitar `/sin-acceso`.
- [ ] **UI-09** La ruta 404 de CodeIgniter no altera `/login`, `/app` ni las rutas de invitación.

## Método de registro de evidencia

| ID | Resultado | Fecha | Ambiente | Evidencia / incidencia |
|---|---|---|---|---|
| INV-01 | Pendiente | — | WAMP local | — |
| INV-02 | Pendiente | — | WAMP local | — |
| AUT-02 | Pendiente | — | WAMP local | — |
| UI-01 | Pendiente | — | WAMP local | — |
| UI-02 | Pendiente | — | WAMP local | — |

**Criterio de cierre INC-001:** no mover el PR a «listo» sin revisar casos de seguridad críticos y resolver incidencias de severidad alta. Los cambios no son producción hasta validar ambiente, SMTP y autorización.
