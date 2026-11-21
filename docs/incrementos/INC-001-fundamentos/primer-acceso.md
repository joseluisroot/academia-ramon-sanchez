# INC-001 — Habilitación de la primera cuenta administrativa

**Alcance:** crear la primera credencial **local** de forma segura, sin agregar nombres, correos ni contraseñas al repositorio.

## Prerrequisitos

- `composer install` finalizó sin errores y Shield está instalado.
- La configuración de MySQL del archivo `.env` apunta a la base local correcta.
- `php spark migrate --all` ejecutó las migraciones de Shield y de la aplicación sin errores.
- `php spark migrate:status` confirma el estado.

## 1. Crear la primera cuenta desde la consola

Reemplazar `TU_USUARIO` por un alias elegido por la persona responsable y `TU_CORREO` por su correo real. **No escribir contraseñas dentro del comando ni compartirlas en el chat.**

```powershell
php spark shield:user create -n TU_USUARIO -e TU_CORREO -g institutional_admin
```

El comando oficial de Shield solicitará la contraseña y su confirmación de manera interactiva. Es la opción recomendada para el arranque, mientras el proceso de invitaciones está pendiente.

> El grupo `institutional_admin` representa a Dennis y fundadores autorizados. Si la persona que prueba el sistema no es una autoridad institucional designada, utilizar en su lugar un usuario de prueba sobre una **base de datos local ficticia** y retirar ese acceso antes de producción.

## 2. Designar la cuenta responsable de asignar roles

Solo después de que la academia haya designado formalmente a un responsable de accesos:

```powershell
php spark shield:user addgroup -n TU_USUARIO -g access_manager
```

La cuenta con `access_manager` podrá abrir `/app/accesos` y gestionar exclusivamente grupos ordinarios. No se permite elevar privilegios institucionales desde ese formulario.

## 3. Comprobar el inicio de sesión

1. Iniciar `php spark serve --host localhost --port 8080`.
2. Abrir `http://localhost:8080/login`.
3. Ingresar con el correo y contraseña creados.
4. Verificar que dirija a `http://localhost:8080/app`.
5. Si se asignó `access_manager`, abrir `http://localhost:8080/app/accesos`.
6. Cerrar sesión y comprobar que `/app` vuelve a requerir autenticación.
7. Comprobar que `/register` no ofrece inscripción pública.

## 4. Comandos de diagnóstico

```powershell
php spark shield:user list
php spark routes
php spark migrate:status
```

Si el usuario figura inactivo, comprobar estado de cuenta antes de utilizar el comando oficial de activación:

```powershell
php spark shield:user activate -n TU_USUARIO
```

## Precauciones

- No crear usuarios institucionales reales sin su autorización.
- No crear un seeder con contraseñas estáticas.
- No registrar credenciales en Git, screenshots ni tickets.
- No exponer el servidor local a Internet sin controles.
- La creación manual mediante CLI es **temporal** para el arranque. La incorporación normal de familias será por invitación individual con token de un solo uso.
- `php spark shield:setup` no debe ejecutarse: el proyecto ya contiene configuración específica de Shield que podría sobrescribirse.

## Verificación pendiente

Estos pasos están documentados, pero solo se considerarán verificados cuando se ejecuten en el entorno WAMP local y se confirme el acceso del usuario autorizado.
