<?= $this->extend('layouts/private') ?>
<?= $this->section('content') ?>
<div class="portal-intro">
  <span class="section-kicker">INC-004 · GESTIÓN DEPORTIVA</span>
  <h1>Entrenamientos</h1>
  <p>Programa sesiones por fecha y categoría. U7/U9 o U11/U13 pueden compartir entrenamiento sin mezclar sus expedientes.</p>
</div>
<?php if (session('error')): ?><div class="auth-alert auth-alert-error" role="alert"><?= esc(session('error')) ?></div><?php endif ?>
<?php if (session('message')): ?><div class="auth-alert auth-alert-success" role="status"><?= esc(session('message')) ?></div><?php endif ?>
<?php if ($canSchedule): ?>
<div class="portal-panel registry-panel">
  <div class="registry-toolbar"><h2>Programar entrenamiento</h2>
    <?php if (auth()->user()?->can('training.assign') && auth()->user()?->inGroup('institutional_admin')): ?>
      <a class="registry-cancel" href="<?= esc(site_url('app/entrenamientos/entrenadores'), 'attr') ?>">Asignar categorías a entrenadores</a>
    <?php endif ?>
  </div>
  <p class="portal-muted">La programación no es una convocatoria de partido. No se genera asistencia anticipada ni se programan repeticiones automáticamente.</p>
  <form class="registry-form" method="post" action="<?= esc(site_url('/app/entrenamientos'), 'attr') ?>">
    <?= csrf_field() ?>
    <div class="registry-fields">
      <label>Fecha <input type="date" name="training_date" min="<?= esc(date('Y-m-d'), 'attr') ?>" required></label>
      <label>Hora de inicio <input type="time" name="starts_at" value="16:30" required></label>
      <label>Hora de finalización <input type="time" name="ends_at" value="18:00" required></label>
      <label>Instalación o lugar <input type="text" name="location_name" maxlength="120" minlength="3" placeholder="Cancha de entrenamiento" required></label>
    </div>
    <fieldset>
      <legend>Categorías participantes</legend>
      <div class="registry-fields">
        <?php foreach ($categories as $category): ?>
          <label><input type="checkbox" name="category_ids[]" value="<?= (int) $category['id'] ?>"> <?= esc($category['code']) ?></label>
        <?php endforeach ?>
      </div>
    </fieldset>
    <div class="registry-actions">
      <button class="portal-action-link registry-submit" type="submit">Programar sesión</button>
      <a class="registry-cancel" href="<?= esc(site_url('/app/atletas'), 'attr') ?>">Registro de atletas</a>
    </div>
  </form>
</div>
<?php else: ?>
<div class="portal-panel">
  <p class="portal-muted">Solo se muestran las sesiones de las categorías asignadas a tu cuenta. La programación de nuevas sesiones corresponde a administración o dirección deportiva.</p>
</div>
<?php endif ?>
<div class="portal-panel">
  <div class="registry-toolbar">
    <h2>Resumen de asistencia</h2>
    <a class="portal-action-link" href="<?= esc(site_url('app/entrenamientos/reporte'), 'attr') ?>">Consultar reporte</a>
  </div>
  <p class="portal-muted">Conteos de presentes, ausentes y justificados registrados en entrenamientos finalizados. Cada entrenador solo consulta sus categorías autorizadas.</p>
</div>
<div class="portal-panel">
  <h2>Últimas sesiones</h2>
  <div class="table-scroll">
    <table class="access-table">
      <thead><tr><th scope="col">Fecha</th><th scope="col">Horario</th><th scope="col">Lugar</th><th scope="col">Estado</th><th scope="col">Acción</th></tr></thead>
      <tbody>
        <?php foreach ($sessions as $session): ?>
          <tr>
            <td><?= esc($session['training_date']) ?></td>
            <td><?= esc(substr($session['starts_at'],0,5) . '–' . substr($session['ends_at'],0,5)) ?></td>
            <td><?= esc($session['location_name']) ?></td>
            <td><?= esc(match ($session['status']) {'scheduled' => 'Programado', 'completed' => 'Finalizado', 'cancelled' => 'Cancelado', default => 'En revisión'}) ?></td>
            <td><a class="registry-cancel" href="<?= esc(site_url('/app/entrenamientos/' . (int) $session['id']), 'attr') ?>">Ver detalle</a></td>
          </tr>
        <?php endforeach ?>
        <?php if ($sessions === []): ?><tr><td colspan="5">Todavía no hay entrenamientos programados.</td></tr><?php endif ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection() ?>
