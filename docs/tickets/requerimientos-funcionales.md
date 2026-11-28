# Requisitos funcionales
| ID | Requisito | Aceptación |
|---|---|---|
| RF01 | Crear evento y tipos con vigencia y capacidad | No emitir por encima del límite |
| RF02 | Emitir lote con correlativos y secretos únicos | Sin duplicados incluso en concurrencia |
| RF03 | Imprimir PDF privado con QR y código manual | Escaneable en papel y móvil |
| RF04 | Asignar y devolver inventario de vendedores | Historial por entrada |
| RF05 | Registrar venta o cortesía autorizada | Activación solo tras operación confirmada |
| RF06 | Anular y reemplazar | Original revocado; vínculo y motivo |
| RF07 | Validar por QR o código manual | Un único ingreso autorizado |
| RF08 | Consultar incidencias | Hora, operador, puerta y motivo |
| RF09 | Reportar inventario y ventas | Conciliación sin doble conteo |
| RF10 | Administrar operadores por evento | Sin acceso cruzado |
| RF11 | Consultar código públicamente (opcional) | Sin consumo ni datos personales |
| RF12 | Consumir beneficios independientes (fase posterior) | Límites atómicos por beneficio |

No permitir reactivar entradas consumidas mediante edición genérica. No exigir cuenta a compradores para MVP; registro mínimo sujeto a aprobación.
