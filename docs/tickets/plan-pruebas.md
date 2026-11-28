# Plan de pruebas
| ID | Caso | Resultado |
|---|---|---|
| QA01 | Venta válida y primer escaneo | Un consumo y auditoría |
| QA02 | Dos dispositivos simultáneos, misma tarjeta | Exactamente un éxito confirmado |
| QA03 | Reintento tras timeout con mismo ID | Resultado original, sin nuevo consumo |
| QA04 | Mismo ID con otro token | Conflicto |
| QA05 | Inexistente, no vendida, anulada | Rechazo |
| QA06 | Evento distinto, usuario revocado, puerta inactiva | Rechazo sin datos ajenos |
| QA07 | Fuera de ventana | Rechazo |
| QA08 | Fallo de escritura auditoría | Rollback |
| QA09 | Venta/emisión concurrente sobre cuota | Sin duplicado ni sobrecapacidad |
| QA10 | GET público y CSRF ausente | Sin consumo |
| QA11 | Flag apagado y prelaunch activo | Portal restringido |
| QA12 | Flag activo y prelaunch activo | Solo rutas permitidas; portal general cerrado |
| QA13 | Cámara móvil, QR impreso y manual | Lectura correcta |
| QA14 | Pérdida de red y recuperación | Sin verde falso, conciliación |
| QA15 | Reemplazo y devolución | Original inválido, sin doble cobro |
| QA16 | Regresión de inicio, 404, login, atletas y entrenamientos | Sin regresión |
Usar datos ficticios y DB de pruebas separada. QA02/09 requieren MySQL real y conexiones independientes, no SQLite. Medir p95 y registrar evidencia. Ninguna prueba de implementación se considera ejecutada en TKT-000.
