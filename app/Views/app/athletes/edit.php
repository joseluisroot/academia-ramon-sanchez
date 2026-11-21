<?= $this->extend('layouts/private') ?>
<?= $this->section('content') ?>
<div class="portal-intro">
  <span class="section-kicker">EXPEDIENTE ADMINISTRATIVO</span>
  <h1>Editar atleta</h1>
  <p>Actualiza el nombre o la categoría deportiva. Cada cambio se registra y conserva la trayectoria de categorías del deportista.</p>
</div>
<?php if (session('error')): ?>
  <div class="auth-alert auth-alert-error" role="alert"><?= esc(session('error')) ?></div>
<?php endif ?>
<?php if (session('message')): ?>
  <div class="auth-alert auth-alert-success" role="status"><?= esc(session('message')) ?></div>
<?php endif ?>
<div class="portal-panel registry-panel">
  <form class="registry-form" method="post" action="<?= esc(site_url('app/atletas/' . (int) $athlete['id']), 'attr') ?>">
    <?= csrf_field() ?>
    <fieldset>
      <legend>Datos del atleta</legend>
      <div class="registry-fields">
        <label>Nombre(s)
          <input type="text" name="first_name" minlength="2" maxlength="100" value="<?= esc($athlete['first_name'], 'attr') ?>" required>
        </label>
        <label>Apellido(s)
          <input type="text" name="last_name" minlength="2" maxlength="100" value="<?= esc($athlete['last_name'], 'attr') ?>" required>
        </label>
        <label>Categoría deportiva
          <select name="sports_category_id" required>
            <?php foreach ($categories as $category): ?>
              <option value="<?= (int) $category['id'] ?>" <?= (int) $athlete['sports_category_id'] === (int) $category['id'] ? 'selected' : '' ?>><?= esc($category['name']) ?></option>
            <?php endforeach ?>
          </select>
        </label>
      </div>
      <p class="portal-muted">La fecha de nacimiento y la fecha de ingreso permanecen protegidas contra modificaciones en este primer corte. Las correcciones excepcionales requerirán un procedimiento específico.</p>
    </fieldset>
    <div class="registry-actions">
      <button class="portal-action-link registry-submit" type="submit">Guardar cambios</button>
      <a class="registry-cancel" href="<?= esc(site_url('app/atletas'), 'attr') ?>">Volver al listado</a>
    </div>
  </form>
</div>
<div class="portal-panel">
  <h2>Historial de categorías</h2>
  <p class="portal-muted">Registro cronológico de la categoría inicial y de los cambios realizados por la administración.</p>
  <div class="table-scroll">
    <table class="access-table">
      <thead><tr><th scope="col">Categoría</th><th scope="col">Vigente desde</th><th scope="col">Motivo</th></tr></thead>
      <tbody>
        <?php foreach ($history as $item): ?>
          <tr>
            <td><?= esc($item['category_code']) ?></td>
            <td><?= esc($item['effective_on']) ?></td>
            <td><?= esc($item['reason'] === 'initial' ? 'Inscripción inicial' : 'Cambio administrativo') ?></td>
          </tr>
        <?php endforeach ?>
        <?php if ($history === []): ?>
          <tr><td colspan="3">No se ha registrado historial de categorías.</td></tr>
        <?php endif ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection() ?>
