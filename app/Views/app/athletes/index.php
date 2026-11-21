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
    <a class="portal-action-link" href="<?= esc(site_url('app/atletas/nuevo'), 'attr') ?>">Registrar atleta</a>
  </div>
  <div class="table-scroll">
    <table class="access-table">
      <thead><tr><th scope="col">Atleta</th><th scope="col">Categoría</th><th scope="col">Fecha de ingreso</th><th scope="col">Estado</th></tr></thead>
      <tbody>
      <?php foreach ($athletes as $athlete): ?>
        <tr>
          <td><strong><?= esc($athlete['first_name'] . ' ' . $athlete['last_name']) ?></strong></td>
          <td><?= esc($athlete['category_code']) ?></td>
          <td><?= esc($athlete['joined_on']) ?></td>
          <td><?= esc($athlete['status'] === 'active' ? 'Activo' : 'En revisión') ?></td>
        </tr>
      <?php endforeach ?>
      <?php if ($athletes === []): ?>
        <tr><td colspan="4">Aún no hay atletas registrados. Inicia con un expediente de prueba ficticio.</td></tr>
      <?php endif ?>
      </tbody>
    </table>
  </div>
  <?= $pager->links() ?>
</div>
<?= $this->endSection() ?>
