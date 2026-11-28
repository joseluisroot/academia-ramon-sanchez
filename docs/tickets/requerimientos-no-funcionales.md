# Requisitos no funcionales
- RNF01: HTTPS y sesión autenticada; mínimo privilegio, CSRF, límites de solicitudes.
- RNF02: operación crítica transaccional en InnoDB; unicidad y reintentos idempotentes.
- RNF03: objetivo inicial p95 inferior a 2 s para validación bajo 5 operadores concurrentes en red de prueba; medir, no prometer.
- RNF04: interfaz responsive, contraste suficiente, resultados con texto además de color.
- RNF05: registro UTC, presentación America/El_Salvador; moneda USD, DECIMAL sin floats.
- RNF06: no almacenar QR secretos en logs, repositorio ni URLs de analítica; PDFs privados.
- RNF07: copias de seguridad y recuperación probada antes de evento; retención aprobada.
- RNF08: regresión del portal y prelanzamiento, PHPUnit, migraciones reproducibles.
