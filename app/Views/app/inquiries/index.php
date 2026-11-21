<?= $this->extend('layouts/private') ?>
<?= $this->section('content') ?>

<div class="portal-intro">
  <span class="section-kicker">ATENCIÓN A FAMILIAS</span>
  <h1>Consultas recibidas</h1>
  <p>Solicitudes de información sobre la academia y primera clase de prueba. Los datos son privados y solo pueden consultarlos responsables autorizados.</p>
</div>

<?php if (session('message') !== null): ?>
  <div class="auth-alert auth-alert-success" role="status"><?= esc(session('message')) ?></div>
<?php endif ?>
<?php if (session('error') !== null): ?>
  <div class="auth-alert auth-alert-error" role="alert"><?= esc(session('error')) ?></div>
<?php endif ?>

<div class="portal-panel">
  <div class="table-scroll">
    <table class="access-table">
      <thead><tr>
        <th scope="col">Contacto adulto</th>
        <th scope="col">Categoría</th>
        <th scope="col">Consulta</th>
        <th scope="col">Fecha</th>
        <th scope="col">Estado / acción</th>
      </tr></thead>
      <tbody>
      <?php foreach ($inquiries as $inquiry): ?>
        <tr>
          <td>
            <strong><?= esc((string) $inquiry['contact_name']) ?></strong>
            <small><?= esc((string) $inquiry['email']) ?></small>
            <?php if ($inquiry['phone'] !== null): ?>
              <small><?= esc((string) $inquiry['phone']) ?></small>
            <?php endif ?>
          </td>
          <td><?= esc($inquiry['category'] === 'not_sure' ? 'Por definir' : $inquiry['category']) ?></td>
          <td><?= esc((string) ($inquiry['message'] ?? 'Solicitud de información')) ?></td>
          <td><?= esc((string) $inquiry['created_at']) ?></td>
          <td>
            <?php if ($inquiry['status'] === 'new' && auth()->user()?->can('inquiries.manage')): ?>
              <form method="post" action="<?= esc(site_url('app/consultas/contactada'), 'attr') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="inquiry_id" value="<?= (int) $inquiry['id'] ?>">
                <button class="inquiry-contact-button" type="submit">Marcar contactada</button>
              </form>
            <?php else: ?>
              <span class="access-locked"><?= esc($inquiry['status'] === 'contacted' ? 'Contactada' : 'Pendiente') ?></span>
            <?php endif ?>
          </td>
        </tr>
      <?php endforeach ?>
      <?php if ($inquiries === []): ?>
        <tr><td colspan="5">Aún no hemos recibido consultas desde el sitio público.</td></tr>
      <?php endif ?>
      </tbody>
    </table>
  </div>
  <?= $pager->links() ?>
</div>

<?= $this->endSection() ?>
