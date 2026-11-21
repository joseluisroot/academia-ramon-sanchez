<?= $this->extend('layouts/private') ?>
<?= $this->section('content') ?>
<div class="portal-intro">
  <span class="section-kicker">GESTIÓN DEPORTIVA</span>
  <h1>Registro de atletas</h1>
  <p>Expedientes privados de deportistas inscritos. Solo personal administrativo autorizado puede consultar este listado.</p>
</div>
<?php if (session('message')): ?>
  <div class="auth-alert auth-alert-success" role="status"><?= esc(session('message')) ?></div>
<?php endif ?>
<div class="portal-panel">
  <div class="registry-toolbar">
    <h2>Atletas registrados</h2>
    <div class="registry-actions">
      <a class="portal-action-link" href="<?= esc(site_url('app/atletas/nuevo'), 'attr') ?>">Registrar atleta</a>
      <?php if (auth()->user()?->can('family.links.verify')): ?>
        <a class="registry-cancel" href="<?= esc(site_url('app/familias/verificar'), 'attr') ?>">Verificar responsables pendientes</a>
      <?php endif ?>
    </div>
  </div>
  <p class="portal-muted">El alta administrativa no habilita automáticamente acceso al expediente. Verifica los vínculos familiares antes de autorizar la consulta desde una cuenta de padre o responsable.</p>
  <div class="table-scroll">
    <table class="access-table">
      <thead><tr><th scope="col">Atleta</th><th scope="col">Categoría</th><th scope="col">Fecha de ingreso</th><th scope="col">Estado</th><th scope="col">Acciones</th></tr></thead>
      <tbody>
      <?php foreach ($athletes as $athlete): ?>
        <tr>
          <td><strong><?= esc($athlete['first_name'] . ' ' . $athlete['last_name']) ?></strong></td>
          <td><?= esc($athlete['category_code']) ?></td>
          <td><?= esc($athlete['joined_on']) ?></td>
          <td><?= esc($athlete['status'] === 'active' ? 'Activo' : 'En revisión') ?></td>
          <td><a class="registry-cancel" href="<?= esc(site_url('app/atletas/' . (int) $athlete['id'] . '/editar'), 'attr') ?>">Editar expediente</a></td>
        </tr>
      <?php endforeach ?>
      <?php if ($athletes === []): ?>
        <tr><td colspan="5">Aún no hay atletas registrados. Inicia con un expediente de prueba ficticio.</td></tr>
      <?php endif ?>
      </tbody>
    </table>
  </div>
  <?= $pager->links() ?>
</div>
<?= $this->endSection() ?>
