# INC-002 — Sitio público y contacto inicial

**Rama:** `feature/inc-002-sitio-publico-contacto`  
**Base:** `feature/inc-001-fundamentos`. **No fusionado**.  
**Estado:** código inicial incorporado; **migraciones y rutas aún no probadas en WAMP**.

## Objetivo

Cerrar el primer recorrido público: presentar la academia con la identidad aprobada, explicar su misión y visión y facilitar a un adulto responsable el contacto por una clase de prueba, sin recopilar datos personales de menores antes del proceso formal de inscripción.

## Funcionalidad incluida

- La portada institucional ya creada en INC-001 conserva el documento oficial de fundadores y escudo.
- Formulario público en `/#contacto`; recepción mediante POST `/solicitar-informacion`.
- Datos mínimos de adulto: nombre, correo, teléfono opcional, categoría de interés y mensaje opcional.
- No se requieren nombres, fecha de nacimiento, fotografía ni información médica de atletas.
- Consentimiento de contacto explícito y aviso en `/privacidad`.
- CSRF global del proyecto, campo señuelo y limitación de tres solicitudes por IP/hora.
- Migración `enrollment_inquiries` y modelo `EnrollmentInquiryModel`.
- Bandeja privada en `/app/consultas`, con paginación de 20 registros.
- Autorización separada `inquiries.view` / `inquiries.manage`, inicialmente para `institutional_admin` y `equipment_manager`.
- Marcar **Contactada** mediante POST protegido y auditoría de quien registró el seguimiento.
- Pruebas unitarias de validaciones `EnrollmentInquiryPolicyTest`.

## Decisiones de diseño

1. **No confundir consulta e inscripción.** Una consulta no crea un atleta, cuenta, obligación de cobro ni expediente.
2. **Proteger información de menores.** El formulario solicita únicamente datos de un adulto.
3. **Solo la academia atiende las solicitudes.** Nadie con rol `guardian` o Junta accede a la bandeja por defecto.
4. **No inventar medios de contacto.** Se utiliza formulario local; teléfono, WhatsApp y redes oficiales se añadirán cuando estén aprobados.
5. **El correo no se envía automáticamente al llenar el formulario.** Se guarda de forma privada y el personal autorizado debe revisar la bandeja.
6. **Retención pendiente.** Antes de producción habrá que aprobar un plazo de conservación, mecanismo de eliminación y procedimiento de consultas sobre datos personales.
7. **No considerar el consentimiento de contacto como autorización para publicar fotografías, estadísticas o perfiles de menores.** Estos consentimientos serán independientes.

## Pruebas pendientes

- [ ] Ejecutar `php spark migrate --all` y comprobar tabla `enrollment_inquiries`.
- [ ] Confirmar `GET /`, `GET /privacidad` y `POST /solicitar-informacion`.
- [ ] Formulario válido de adulto: guardar una sola consulta; mensaje de confirmación.
- [ ] Correo inválido, nombre de longitud incorrecta, teléfono inválido y categoría fuera del catálogo: rechazados.
- [ ] Petición sin consentimiento y POST sin CSRF: rechazados.
- [ ] Campo señuelo rellenado: no crear solicitud.
- [ ] Exceder tres solicitudes por IP/hora: rechazado.
- [ ] Usuario institucional con `inquiries.view`: puede abrir `/app/consultas`.
- [ ] Usuario con `inquiries.manage`: puede marcar una solicitud nueva como contactada.
- [ ] Una solicitud ya contactada no puede volver a marcarse; la transición queda auditada.
- [ ] Cuenta `guardian`: **no** puede consultar la bandeja ni cambiar estados.
- [ ] Comprobar página 404 institucional corregida en INC-001B y código HTTP 404 real.
- [ ] Revisar móvil, foco visible, encabezados y mensajes de error.
- [ ] Revisar privacidad, retención y texto final del consentimiento con los fundadores.

## Validación local

```powershell
git switch feature/inc-002-sitio-publico-contacto
git pull --ff-only origin feature/inc-002-sitio-publico-contacto
php spark migrate --all
php spark migrate:status
php spark routes
vendor\\bin\\phpunit --bootstrap vendor/autoload.php tests/unit/EnrollmentInquiryPolicyTest.php
```

La bandeja está en `http://localhost:8080/app/consultas` y el formulario en `http://localhost:8080/#contacto`.

**No introducir solicitudes reales de padres o menores hasta confirmar seguridad, acceso restringido y política de retención.**
