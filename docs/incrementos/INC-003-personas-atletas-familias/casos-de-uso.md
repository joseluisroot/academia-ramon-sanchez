# Casos de uso y límites del INC-003

## UC-003-01 — Registrar atleta

**Actor:** administrador institucional o utilero con `athletes.manage`.  
**Acceso:** `/app/atletas/nuevo`.  
**Resultado:** atleta y adulto responsable asociados, sin habilitar automáticamente consulta familiar.

## UC-003-02 — Registrar varios hijos del mismo adulto

**Actor:** personal de inscripción.  
**Acceso:** selección de persona adulta registrada.  
**Resultado:** múltiples relaciones `athlete_guardians` referidas a una sola persona. El sistema no replica datos personales cuando existen varios atletas en la familia.

## UC-003-03 — Verificar cuenta familiar

**Actor:** administrador institucional con permiso `family.links.verify`.  
**Acceso:** `/app/familias/verificar`.  
**Requisito:** identidad y relación de responsabilidad comprobadas previamente por medios institucionales; cuenta Shield creada e invitación aceptada.  
**Resultado:** asociación `persons.user_id` y marca de vínculo verificado. Auditoría sin datos personales.

## UC-003-04 — Consultar mis atletas

**Actor:** padre o responsable autenticado `guardian`.  
**Acceso:** `/app/mis-atletas`.  
**Resultado:** únicamente atletas activos cuya relación con la cuenta está verificada. En ausencia de vinculación, se muestra un estado vacío sin exponer listados globales.

## Pendientes de negocio

El registro de otro adulto responsable, edición de fichas, verificación documental formal, disputas de relación familiar, separación de permisos de imagen y política de dorsales se definirán en cortes posteriores de INC-003 antes de habilitar el sistema en producción.
