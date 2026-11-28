# Reglas de negocio propuestas
RN01: evento DRAFT → OPEN → CLOSED o CANCELLED; solo durante ventana de acceso se consume.
RN02: ticket CREATED → PRINTED → ASSIGNED → SOLD → REDEEMED; impresión y asignación se auditan, venta directa puede omitir asignación. CANCELLED es terminal; devolución de inventario no vendido permite reasignación. Cortesía activa mediante registro explícito de valor cero autorizado.
RN03: correlativo único por evento; token aleatorio global único, no derivado del correlativo.
RN04: vender y emitir en transacción; impedir sobrecapacidad y venta duplicada.
RN05: ingreso conjunto por defecto propuesto; si se aprueba ingreso separado, migrar a consumos por persona/beneficio antes de emitir.
RN06: rechazo de código consumido no prueba fraude; supervisor revisa comprador e historial.
RN07: reemplazo anula original no consumido y crea nuevo identificador en una transacción; conservar venta y capacidad, sin doble ingreso ni doble cobro. Incidencias de entradas consumidas no se resuelven por reinicio de estado.
RN08: ventas y devoluciones financieras trazables, sin borrado; reembolso requiere autorización y registro separado.
RN09: no habilitar venta ni consumo de evento cerrado, salvo conciliación administrativa sin acceso.
RN10: consulta pública GET jamás consume. Reintento de misma solicitud devuelve resultado original identificado como reintento, sin segundo ingreso.
