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
  <h2>Estado del atleta</h2>
  <p class="portal-muted">Estado actual: <strong><?= esc(match ($athlete['status']) {'active' => 'Activo', 'inactive' => 'Inactivo', 'withdrawn' => 'Retirado', default => 'En revisión'}) ?></strong>. Los cambios conservan historial; un retiro definitivo no se reactiva desde este formulario.</p>
  <?php if ($athlete['status'] !== 'withdrawn'): ?>
    <form class="registry-form" method="post" action="<?= esc(site_url('app/atletas/' . (int) $athlete['id'] . '/estado'), 'attr') ?>">
      <?= csrf_field() ?>
      <div class="registry-fields">
        <label>Nuevo estado
          <select name="status" required>
            <option value="">Seleccionar</option>
            <?php if ($athlete['status'] === 'active'): ?>
              <option value="inactive">Inactivo</option>
              <option value="withdrawn">Retirado</option>
            <?php else: ?>
              <option value="active">Activo</option>
              <option value="withdrawn">Retirado</option>
            <?php endif ?>
          </select>
        </label>
        <label>Motivo administrativo
          <select name="reason" required>
            <option value="">Seleccionar motivo</option>
            <option value="temporary_pause">Pausa temporal</option>
            <option value="family_request">Solicitud familiar</option>
            <option value="administrative">Decisión administrativa</option>
            <option value="returned">Reincorporación</option>
          </select>
        </label>
      </div>
      <div class="registry-actions"><button type="submit" class="portal-action-link registry-submit">Guardar estado</button></div>
    </form>
  <?php endif ?>
  <h3 class="registry-subheading">Historial de estados</h3>
  <div class="table-scroll">
    <table class="access-table">
      <thead><tr><th scope="col">Anterior</th><th scope="col">Nuevo</th><th scope="col">Motivo</th><th scope="col">Fecha</th></tr></thead>
      <tbody>
        <?php foreach ($statusHistory as $entry): ?>
          <tr>
            <td><?= esc($entry['from_status']) ?></td>
            <td><?= esc($entry['to_status']) ?></td>
            <td><?= esc($entry['reason_code']) ?></td>
            <td><?= esc($entry['changed_at']) ?></td>
          </tr>
        <?php endforeach ?>
        <?php if ($statusHistory === []): ?><tr><td colspan="4">Sin cambios de estado registrados.</td></tr><?php endif ?>
      </tbody>
    </table>
  </div>
</div>

<div class="portal-panel">
  <h2>Seguimiento de consentimientos</h2>
  <p class="portal-muted"><strong>Atención:</strong> esta pantalla solo permite registrar documentación pendiente, denegada o revocada. No acredita una autorización legal ni habilita publicar imágenes o datos de menores. Los documentos, versiones y firmas deberán definirse institucionalmente antes de la producción.</p>
  <form class="registry-form" method="post" action="<?= esc(site_url('app/atletas/' . (int) $athlete['id'] . '/documentacion'), 'attr') ?>">
    <?= csrf_field() ?>
    <div class="registry-fields">
      <label>Documento o consentimiento
        <select name="type" required>
          <option value="">Seleccionar</option>
          <option value="image_use">Uso de imagen</option>
          <option value="data_notice">Aviso de tratamiento de datos</option>
        </select>
      </label>
      <label>Estado documental
        <select name="state" required>
          <option value="">Seleccionar</option>
          <option value="pending">Pendiente</option>
          <option value="declined">Denegado</option>
          <option value="revoked">Revocado</option>
        </select>
      </label>
    </div>
    <div class="registry-actions"><button class="portal-action-link registry-submit" type="submit">Registrar estado documental</button></div>
  </form>
  <h3 class="registry-subheading">Historial documental</h3>
  <div class="table-scroll">
    <table class="access-table">
      <thead><tr><th scope="col">Tipo</th><th scope="col">Estado</th><th scope="col">Fecha</th></tr></thead>
      <tbody>
        <?php foreach ($consentHistory as $entry): ?>
          <tr>
            <td><?= esc($entry['consent_type'] === 'image_use' ? 'Uso de imagen' : 'Aviso de datos') ?></td>
            <td><?= esc(match ($entry['record_state']) {'pending' => 'Pendiente', 'declined' => 'Denegado', 'revoked' => 'Revocado', default => 'Sin validar'}) ?></td>
            <td><?= esc($entry['recorded_at']) ?></td>
          </tr>
        <?php endforeach ?>
        <?php if ($consentHistory === []): ?><tr><td colspan="3">Sin registros documentales. No existe autorización de imagen.</td></tr><?php endif ?>
      </tbody>
    </table>
  </div>
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
              <?php elseif ($guardian['verified_at'] === null && ! (bool) $guardian['is_primary'] && auth()->user()?->can('family.links.verify')): ?>
                <form method="post"
                      action="<?= esc(site_url('app/atletas/' . (int) $athlete['id'] . '/responsables/quitar-pendiente'), 'attr') ?>"
                      onsubmit="return confirm('¿Quitar esta relación pendiente? El adulto seguirá registrado y podrás asociarlo nuevamente.');">
                  <?= csrf_field() ?>
                  <input type="hidden" name="relation_id" value="<?= (int) $guardian['id'] ?>">
                  <button type="submit" class="registry-danger">Quitar pendiente</button>
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
