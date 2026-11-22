<?= $this->extend('layouts/private') ?>
<?= $this->section('content') ?>
<div class="portal-intro">
  <span class="section-kicker">GESTIÓN DEPORTIVA · PERMISOS</span>
  <h1>Categorías asignadas a entrenadores</h1>
  <p>La asignación permite que cada entrenador consulte las sesiones y registre asistencia únicamente de los atletas de sus categorías autorizadas.</p>
</div>
<?php if (session('error')): ?><div class="auth-alert auth-alert-error" role="alert"><?= esc(session('error')) ?></div><?php endif ?>
<?php if (session('message')): ?><div class="auth-alert auth-alert-success" role="status"><?= esc(session('message')) ?></div><?php endif ?>
<div class="portal-panel registry-panel">
  <div class="registry-toolbar"><h2>Asignar categoría</h2><a class="registry-cancel" href="<?= esc(site_url('app/entrenamientos'), 'attr') ?>">Volver a entrenamientos</a></div>
  <p class="portal-muted">Primero invita y activa al usuario con rol Entrenador. Esta pantalla no concede roles ni permite acceder al expediente administrativo de atletas.</p>
  <form class="registry-form" method="post" action="<?= esc(site_url('app/entrenamientos/entrenadores'), 'attr') ?>">
    <?= csrf_field() ?>
    <div class="registry-fields">
      <label>Entrenador
        <select name="user_id" required>
          <option value="">Seleccionar entrenador</option>
          <?php foreach ($coaches as $coach): ?>
            <option value="<?= (int) $coach['id'] ?>"><?= esc($coach['email']) ?> · usuario #<?= (int) $coach['id'] ?></option>
          <?php endforeach ?>
        </select>
      </label>
      <label>Categoría
        <select name="sports_category_id" required>
          <option value="">Seleccionar categoría</option>
          <?php foreach ($categories as $category): ?>
            <option value="<?= (int) $category['id'] ?>"><?= esc($category['code'] . ' · ' . $category['name']) ?></option>
          <?php endforeach ?>
        </select>
      </label>
    </div>
    <input type="hidden" name="operation" value="assign">
    <button class="portal-action-link registry-submit" type="submit">Asignar categoría</button>
  </form>
</div>
<div class="portal-panel">
  <h2>Asignaciones registradas</h2>
  <div class="table-scroll">
    <table class="access-table">
      <thead><tr><th scope="col">Usuario entrenador</th><th scope="col">Categoría</th><th scope="col">Estado</th><th scope="col">Asignación</th><th scope="col">Acción</th></tr></thead>
      <tbody>
        <?php foreach ($assignments as $assignment): ?>
          <tr>
            <td>#<?= (int) $assignment['user_id'] ?></td>
            <td><?= esc($assignment['category_code']) ?></td>
            <td><?= (bool) $assignment['active'] ? 'Activa' : 'Revocada' ?></td>
            <td><?= esc($assignment['assigned_at']) ?></td>
            <td>
              <?php if ((bool) $assignment['active']): ?>
                <form method="post" action="<?= esc(site_url('app/entrenamientos/entrenadores'), 'attr') ?>" onsubmit="return confirm('¿Revocar el acceso de este entrenador a la categoría?');">
                  <?= csrf_field() ?>
                  <input type="hidden" name="user_id" value="<?= (int) $assignment['user_id'] ?>">
                  <input type="hidden" name="sports_category_id" value="<?= (int) $assignment['sports_category_id'] ?>">
                  <input type="hidden" name="operation" value="revoke">
                  <button type="submit" class="registry-danger">Revocar acceso</button>
                </form>
              <?php else: ?>
                <span class="portal-muted">Sin acceso</span>
              <?php endif ?>
            </td>
          </tr>
        <?php endforeach ?>
        <?php if ($assignments === []): ?><tr><td colspan="5">Aún no existen categorías asignadas a entrenadores.</td></tr><?php endif ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection() ?>
