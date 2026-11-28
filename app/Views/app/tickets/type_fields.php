<?php $type=$type??[]; ?>
<label>Nombre del tipo de entrada <input name="name" required maxlength="100" value="<?= esc($type['name']??'','attr') ?>"></label>
<label>Precio por tarjeta (USD) <input name="price" type="number" min="0" step="0.01" required value="<?= esc((string)($type['price']??''),'attr') ?>"></label>
<label>Adultos por tarjeta <input name="adult_qty" type="number" min="0" max="100" required value="<?= esc((string)($type['adult_qty']??1),'attr') ?>"></label>
<label>Alumnos por tarjeta <input name="student_qty" type="number" min="0" max="100" required value="<?= esc((string)($type['student_qty']??1),'attr') ?>"></label>
<label>Cantidad máxima de tarjetas <input name="quota" type="number" min="1" max="100000" required value="<?= esc((string)($type['quota']??''),'attr') ?>"></label>
<output class="tickets-estimate" aria-live="polite"></output>
