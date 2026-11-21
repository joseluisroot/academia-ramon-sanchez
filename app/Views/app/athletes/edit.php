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
<div class="portal-panel">
  <h2>Dorsal deportivo</h2>
  <p class="portal-muted">El dorsal es exclusivo dentro de cada categoría. No se traslada automáticamente al cambiar de categoría: primero debes liberarlo y después realizar el cambio autorizado.</p>
  <?php if ($currentJersey !== null): ?>
    <p><strong>Dorsal actual: #<?= (int) $currentJersey['jersey_number'] ?></strong></p>
    <form method="post" action="<?= esc(site_url('app/atletas/' . (int) $athlete['id'] . '/dorsal/liberar'), 'attr') ?>">
      <?= csrf_field() ?>
      <button class="registry-danger" type="submit">Liberar dorsal actual</button>
    </form>
  <?php else: ?>
    <p class="portal-muted">Este atleta no tiene dorsal asignado.</p>
  <?php endif ?>
  <form class="jersey-form" method="post" action="<?= esc(site_url('app/atletas/' . (int) $athlete['id'] . '/dorsal'), 'attr') ?>">
    <?= csrf_field() ?>
    <label>Dorsal a asignar (1 a 99)
      <input type="number" name="jersey_number" min="1" max="99" required>
    </label>
    <button type="submit" class="portal-action-link registry-submit"><?= $currentJersey === null ? 'Asignar dorsal' : 'Cambiar dorsal' ?></button>
  </form>
  <h3 class="registry-subheading">Historial de dorsales</h3>
  <div class="table-scroll">
    <table class="access-table">
      <thead><tr><th scope="col">Dorsal</th><th scope="col">Categoría</th><th scope="col">Movimiento</th><th scope="col">Fecha</th></tr></thead>
      <tbody>
      <?php foreach ($jerseyHistory as $entry): ?>
        <tr>
          <td>#<?= (int) $entry['jersey_number'] ?></td>
          <td><?= esc($entry['category_code']) ?></td>
          <td><?= esc($entry['event'] === 'assigned' ? 'Asignado' : 'Liberado') ?></td>
          <td><?= esc($entry['occurred_at']) ?></td>
        </tr>
      <?php endforeach ?>
      <?php if ($jerseyHistory === []): ?>
        <tr><td colspan="4">No hay movimientos de dorsales.</td></tr>
      <?php endif ?>
      </tbody>
    </table>
  </div>
</div>

<div class="portal-panel">
  <h2>Responsables del atleta</h2>
  <p class="portal-muted">Puedes registrar más de un adulto responsable. La relación se mantiene sin acceso digital hasta su verificación por administración institucional.</p>
  <div class="table-scroll">
    <table class="access-table">
      <thead><tr><th scope="col">Adulto</th><th scope="col">Relación</th><th scope="col">Vinculación</th><th scope="col">Acción</th></tr></thead>
      <tbody>
        <?php foreach ($guardians as $guardian): ?>
          <tr>
            <td><?= esc($guardian['first_name'] . ' ' . $guardian['last_name']) ?></td>
            <td><?= esc(match ($guardian['relationship']) {
                'mother' => 'Madre', 'father' => 'Padre',
                'legal_guardian' => 'Representante legal',
                default => 'Adulto autorizado',
            }) ?></td>
            <td><?= esc($guardian['verified_at'] === null ? 'Pendiente de verificación' : 'Verificada') ?></td>
            <td>
              <?php if ($guardian['verified_at'] !== null && auth()->user()?->can('family.links.verify')): ?>
                <form method="post" action="<?= esc(site_url('app/atletas/' . (int) $athlete['id'] . '/responsables/revocar'), 'attr') ?>">
                  <?= csrf_field() ?>
                  <input type="hidden" name="relation_id" value="<?= (int) $guardian['id'] ?>">
                  <button type="submit" class="registry-danger">Revocar acceso</button>
                </form>
              <?php else: ?>
                <span class="access-locked">Sin acciones disponibles</span>
              <?php endif ?>
            </td>
          </tr>
        <?php endforeach ?>
        <?php if ($guardians === []): ?>
          <tr><td colspan="4">No existen responsables asociados.</td></tr>
        <?php endif ?>
      </tbody>
    </table>
  </div>
  <h3 class="registry-subheading">Añadir otro responsable existente</h3>
  <form class="registry-form" method="post" action="<?= esc(site_url('app/atletas/' . (int) $athlete['id'] . '/responsables'), 'attr') ?>">
    <?= csrf_field() ?>
    <div class="registry-fields">
      <label>Adulto registrado
        <select name="person_id" required>
          <option value="">Seleccionar adulto</option>
          <?php foreach ($adults as $adult): ?>
            <option value="<?= (int) $adult['id'] ?>"><?= esc($adult['first_name'] . ' ' . $adult['last_name']) ?> · #<?= (int) $adult['id'] ?></option>
          <?php endforeach ?>
        </select>
      </label>
      <label>Relación
        <select name="relationship" required>
          <option value="">Seleccionar relación</option>
          <option value="mother">Madre</option>
          <option value="father">Padre</option>
          <option value="legal_guardian">Representante legal</option>
          <option value="other_authorized">Otro adulto autorizado</option>
        </select>
      </label>
    </div>
    <div class="registry-actions">
      <button type="submit" class="portal-action-link registry-submit">Añadir responsable</button>
      <?php if (auth()->user()?->can('family.links.verify')): ?>
        <a href="<?= esc(site_url('app/familias/verificar'), 'attr') ?>" class="registry-cancel">Revisar vínculos pendientes</a>
      <?php endif ?>
    </div>
  </form>
  <h3 class="registry-subheading">Registrar un nuevo adulto responsable</h3>
  <p class="portal-muted">Completa esta sección únicamente si la persona no existe en el catálogo de adultos. Su acceso a los datos del atleta no estará autorizado hasta completar la verificación de identidad y parentesco.</p>
  <form class="registry-form" method="post" action="<?= esc(site_url('app/atletas/' . (int) $athlete['id'] . '/responsables/nuevo'), 'attr') ?>">
    <?= csrf_field() ?>
    <div class="registry-fields">
      <label>Nombre(s) del adulto
        <input type="text" name="first_name" minlength="2" maxlength="100" autocomplete="off" required>
      </label>
      <label>Apellido(s) del adulto
        <input type="text" name="last_name" minlength="2" maxlength="100" autocomplete="off" required>
      </label>
      <label>Teléfono (opcional)
        <input type="tel" name="phone" maxlength="30" autocomplete="off">
      </label>
      <label>Relación
        <select name="relationship" required>
          <option value="">Seleccionar relación</option>
          <option value="mother">Madre</option>
          <option value="father">Padre</option>
          <option value="legal_guardian">Representante legal</option>
          <option value="other_authorized">Otro adulto autorizado</option>
        </select>
      </label>
    </div>
    <div class="registry-actions">
      <button type="submit" class="portal-action-link registry-submit">Registrar adulto y asociarlo</button>
    </div>
  </form>
</div>

<?= $this->endSection() ?>
