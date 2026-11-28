# Pruebas de aceptación pendientes
| Modo | Flag | Sesión | Ruta | Esperado |
|---|---|---|---|---|
| prelaunch | false | cualquiera | /tickets | 404 |
| full | false | cualquiera | /tickets | 404 |
| prelaunch | true | no | /tickets | login |
| prelaunch | true | administrador | /tickets | workspace |
| prelaunch | true | operador tickets | /tickets | workspace |
| prelaunch | true | padre sin permiso | /tickets | 403 |
| prelaunch | true | administrador | /app/atletas | redirección portada |
| prelaunch | true | administrador | POST /app/atletas | 404 |
| prelaunch | true | cualquiera | /register | bloqueado |
| full | true | administrador | /app | navegación Tickets |
Probar login directo, retorno al workspace, logout, CSRF, intentos fallidos, magic link, acciones MFA si configuradas, cache-control y flag apagado tras sesión iniciada. Probar rutas desde virtual host WAMP; verificar subdirectorios/normalización de URI. Confirmar que ninguna respuesta privada se sirve desde caché. PHPUnit incluido para política pura; las pruebas HTTP requieren entorno local. No marcar completado hasta evidencias.
