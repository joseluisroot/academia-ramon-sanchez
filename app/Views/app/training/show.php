<?= $this->extend('layouts/private') ?>
<?= $this->section('content') ?>
<div class="portal-intro">
  <span class="section-kicker">CONTROL DE ASISTENCIA</span>
  <h1>Entrenamiento del <?= esc($session['training_date']) ?></h1>
  <p><?= esc(substr($session['starts_at'],0,5) . '–' . substr($session['ends_at'],0,5)) ?> · <?= esc($session['location_name']) ?></p>
</div>
<?php if (session('error')): ?><div class="auth-alert auth-alert-error" role="alert"><?= esc(session('error')) ?></div><?php endif ?>
<?php if (session('message')): ?><div class="auth-alert auth-alert-success" role="status"><?= esc(session('message')) ?></div><?php endif ?>
<div class="portal-panel">
  <div class="registry-toolbar">
    <h2>Categorías participantes</h2>
    <a class="registry-cancel" href="<?= esc(site_url('/app/entrenamientos'), 'attr') ?>">Volver a sesiones</a>
  </div>
  <p class="portal-muted">
    <?php foreach ($categories as $category): ?><?= esc($category['category_code']) ?> &nbsp; <?php endforeach ?>
  </p>
  <?php if (! $canRecord): ?>
    <p class="portal-muted">La asistencia se habilita desde la fecha del entrenamiento, mientras la sesión esté programada.</p>
  <?php endif ?>
  <div class="table-scroll">
    <table class="access-table">
      <thead><tr><th scope="col">Atleta</th><th scope="col">Categoría actual</th><th scope="col">Asistencia registrada</th><th scope="col">Actualizar</th></tr></thead>
      <tbody>
        <?php foreach ($athletes as $athlete): ?>
          <tr>
            <td><strong><?= esc($athlete['first_name'] . ' ' . $athlete['last_name']) ?></strong></td>
            <td><?= esc($athlete['category_code']) ?></td>
            <td><?= esc(match ($athlete['attendance_status']) {'present' => 'Presente', 'absent' => 'Ausente', 'excused' => 'Justificado', default => 'Sin marcar'}) ?></td>
            <td>
              <?php if ($canRecord): ?>
                <form method="post" action="<?= esc(site_url('/app/entrenamientos/' . (int) $session['id'] . '/asistencia'), 'attr') ?>">
                  <?= csrf_field() ?>
                  <input type="hidden" name="athlete_id" value="<?= (int) $athlete['id'] ?>">
                  <label>Estado
                    <select name="status" aria-label="Asistencia de <?= esc($athlete['first_name'] . ' ' . $athlete['last_name'], 'attr') ?>" required>
                      <option value="">Seleccionar</option>
                      <option value="present">Presente</option>
                      <option value="absent">Ausente</option>
                      <option value="excused">Justificado</option>
                    </select>
                  </label>
                  <button class="portal-action-link registry-submit" type="submit">Guardar</button>
                </form>
              <?php else: ?>—<?php endif ?>
            </td>
          </tr>
        <?php endforeach ?>
        <?php if ($athletes === []): ?><tr><td colspan="4">No hay atletas activos en las categorías de esta sesión.</td></tr><?php endif ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection() ?>
