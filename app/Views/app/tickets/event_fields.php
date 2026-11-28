<?php $event=$event??[]; ?>
<label>Nombre del evento <input name="name" required maxlength="150" value="<?= esc($event['name']??'', 'attr') ?>"></label>
<label>Identificador URL (automático, editable) <input name="slug" maxlength="100" pattern="[a-z0-9]+(-[a-z0-9]+)*" value="<?= esc($event['slug']??'', 'attr') ?>"></label>
<label>Lugar del evento <input name="venue" required maxlength="250" value="<?= esc($event['venue']??'', 'attr') ?>"></label>
<?php foreach (['starts_at'=>'Inicio del evento','access_from'=>'Apertura de acceso','access_to'=>'Cierre de acceso'] as $field=>$label): ?>
<label><?= esc($label) ?> (hora de El Salvador) <input type="datetime-local" name="<?= esc($field,'attr') ?>" required value="<?= esc(isset($event[$field]) ? \App\Services\Tickets\EventPolicy::local($event[$field]) : '', 'attr') ?>"></label>
<?php endforeach ?>
<label>Capacidad total de personas <input type="number" name="capacity" min="1" max="100000" required value="<?= esc((string)($event['capacity']??''),'attr') ?>"></label>
<small>La apertura se sugiere 30 minutos antes y el cierre 3 horas después. Puedes ajustar ambos horarios.</small>
