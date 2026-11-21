<?= $this->extend('layouts/private') ?>
<?= $this->section('content') ?>
<div class="portal-intro">
  <span class="section-kicker">SEGURIDAD INSTITUCIONAL</span>
  <h1>Administración de accesos</h1>
  <p>Solo el responsable designado puede asignar permisos ordinarios. Las cuentas privilegiadas y los accesos sensibles no se modifican desde esta pantalla.</p>
</div>

<?php if (session('message') !== null): ?>
  <div class="auth-alert auth-alert-success" role="status"><?= esc(session('message')) ?></div>
<?php endif ?>
<?php if (session('error') !== null): ?>
  <div class="auth-alert auth-alert-error" role="alert"><?= esc(session('error')) ?></div>
<?php endif ?>

<div class="portal-panel">
  <h2>Usuarios existentes</h2>
  <p class="portal-muted">Se muestran únicamente identificadores de cuenta. No compartas datos de atletas ni información financiera al solicitar cambios de acceso.</p>
  <div class="table-scroll">
    <table class="access-table">
      <thead><tr><th scope="col">Usuario</th><th scope="col">Grupos actuales</th><th scope="col">Acción</th></tr></thead>
      <tbody>
      <?php foreach ($users as $user): ?>
        <?php $currentGroups = $user->getGroups() ?? []; ?>
        <?php $protected = (int) $user->id === (int) auth()->id()
            || count(array_intersect(['institutional_admin', 'access_manager', 'technical_support'], $currentGroups)) > 0; ?>
        <tr>
          <td><strong><?= esc((string) $user->username) ?></strong><small>Cuenta #<?= (int) $user->id ?></small></td>
          <td><?= esc(implode(', ', $currentGroups) ?: 'Sin grupos') ?></td>
          <td>
            <?php if ($protected): ?>
              <span class="access-locked">Cuenta protegida</span>
            <?php else: ?>
              <form class="access-form" method="post" action="<?= esc(site_url('app/accesos'), 'attr') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="user_id" value="<?= (int) $user->id ?>">
                <label>Grupo
                  <select name="group" required>
                    <?php foreach ($assignableGroups as $group): ?>
                      <option value="<?= esc($group, 'attr') ?>"><?= esc($group) ?></option>
                    <?php endforeach ?>
                  </select>
                </label>
                <label>Operación
                  <select name="operation" required>
                    <option value="grant">Asignar</option>
                    <option value="revoke">Retirar</option>
                  </select>
                </label>
                <label>Motivo
                  <input type="text" name="reason" minlength="10" maxlength="250" required placeholder="Indica por qué se realiza el cambio">
                </label>
                <button type="submit">Aplicar cambio</button>
              </form>
            <?php endif ?>
          </td>
        </tr>
      <?php endforeach ?>
      </tbody>
    </table>
  </div>
  <?= $pager->links() ?>
</div>
<?= $this->endSection() ?>
