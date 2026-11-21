# Casos de uso — INC-002

## UC-002-01: adulto solicita información

**Actor:** adulto interesado en la academia.  
**Entrada:** nombre, correo, teléfono opcional, categoría, mensaje opcional, aceptación del aviso.  
**Resultado:** solicitud privada con estado `new`.

**Reglas:** campo de menores no disponible; CSRF y límites por IP; no crea cuenta ni expediente; la academia no asegura respuesta inmediata ni confirma reserva de clase.

## UC-002-02: personal autorizado atiende consulta

**Actor:** usuario con `inquiries.view` y `inquiries.manage`.  
**Acceso:** `/app/consultas`.  
**Resultado:** visualizar solicitudes y marcar manualmente `contacted`. Se registra usuario y fecha.

**Reglas:** denegado por defecto, estado anterior `new`, cambios transaccionales y auditable. El listado solo se presenta al personal autorizado; no es visible a familias ni junta.

## UC-002-03: familia consulta aviso de privacidad

**Actor:** visitante.  
**Acceso:** `/privacidad`.  
**Resultado:** conoce el uso de datos mínimos, los destinatarios internos y la diferencia entre contacto e inscripción.

## No incluido

- Alta formal de atletas, validación de responsables legales, afiliación a categorías, dorsales, uniformes, cobros, fotografías de menores y perfiles públicos.
- Automatización de WhatsApp/correo de respuesta y conversión a matrícula.
- Flujo de borrado/retención programada (debe implementarse antes de producción).
- Gestión completa de consultas y asignación por categoría; primera etapa permite solo `new` → `contacted`.

Estas funciones corresponden a incrementos posteriores, principalmente INC-003 para personas y atletas.
