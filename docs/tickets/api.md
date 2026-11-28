# Contrato HTTP propuesto
| Método | Ruta | Acción |
|---|---|---|
| GET | /app/eventos/{id}/validar | UI operador |
| POST | /app/eventos/{id}/validar | Consumir acceso |
| GET | /tickets/verificar | Consulta pública opcional sin secreto en path |
| POST | /tickets/verificar | Consultar sin consumo (limitado) |
| POST | /app/eventos/{id}/entradas | Emitir |
| POST | /app/eventos/{id}/entradas/{ticketId}/venta | Activar |
| POST | /app/eventos/{id}/entradas/{ticketId}/anular | Anular |
Petición consumo JSON: token o manual_code (exactamente uno), access_point_id, request_id UUID; CSRF según configuración. El servidor determina operador de sesión, no del cuerpo. Respuesta JSON: result, request_id, replayed, serial_number cuando autorizado, redeemed_at, entitlement_summary cuando corresponda. 200 resultado de negocio (AUTHORIZED, ALREADY_USED, CANCELLED, NOT_ACTIVE, INVALID, OUTSIDE_WINDOW); 400 formato, 401 sesión, 403 permiso, 409 idempotencia incompatible, 429 límite, 503 indisponible. Nunca autorizar ante error ni consumir con consulta pública. Rutas definitivas en TKT-001/005.
