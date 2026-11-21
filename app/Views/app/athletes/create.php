<?= $this->extend('layouts/private') ?>
<?= $this->section('content') ?>
<div class="portal-intro">
  <span class="section-kicker">INSCRIPCIÓN ADMINISTRATIVA</span>
  <h1>Registrar un atleta</h1>
  <p>Completa los datos mínimos del deportista y su adulto responsable. Esta etapa no crea una cuenta de acceso ni autoriza la publicación de información del menor.</p>
</div>
<?php if (session('error')): ?>
  <div class="auth-alert auth-alert-error" role="alert"><?= esc(session('error')) ?></div>
<?php endif ?>
<div class="portal-panel registry-panel">
  <form class="registry-form" method="post" action="<?= esc(site_url('app/atletas'), 'attr') ?>">
    <?= csrf_field() ?>
    <fieldset>
      <legend>Datos del atleta</legend>
      <div class="registry-fields">
        <label>Nombre(s)
          <input type="text" name="first_name" minlength="2" maxlength="100" autocomplete="off" required>
        </label>
        <label>Apellido(s)
          <input type="text" name="last_name" minlength="2" maxlength="100" autocomplete="off" required>
        </label>
        <label>Fecha de nacimiento
          <input type="date" name="birth_date" required>
        </label>
        <label>Fecha de ingreso
          <input type="date" name="joined_on" max="<?= esc(date('Y-m-d'), 'attr') ?>" required>
        </label>
        <label>Categoría inicial
          <select name="sports_category_id" required>
            <option value="">Seleccionar categoría</option>
            <?php foreach ($categories as $category): ?>
              <option value="<?= (int) $category['id'] ?>"><?= esc($category['name']) ?></option>
            <?php endforeach ?>
          </select>
        </label>
      </div>
    </fieldset>
    <fieldset>
      <legend>Adulto responsable</legend>
      <p class="portal-muted">El parentesco se registra administrativamente; el vínculo con una cuenta digital queda pendiente de revisión independiente.</p>
      <div class="registry-fields">
        <label>Nombre(s)
          <input type="text" name="guardian_first_name" minlength="2" maxlength="100" autocomplete="off" required>
        </label>
        <label>Apellido(s)
          <input type="text" name="guardian_last_name" minlength="2" maxlength="100" autocomplete="off" required>
        </label>
        <label>Teléfono (opcional)
          <input type="tel" name="guardian_phone" maxlength="30" autocomplete="off">
        </label>
        <label>Relación con el atleta
          <select name="relationship" required>
            <option value="">Seleccionar relación</option>
            <option value="mother">Madre</option>
            <option value="father">Padre</option>
            <option value="legal_guardian">Representante legal</option>
            <option value="other_authorized">Otro adulto autorizado</option>
          </select>
        </label>
      </div>
    </fieldset>
    <p class="portal-muted">No registres información médica, documentos de identidad o imágenes en esta primera etapa.</p>
    <div class="registry-actions">
      <button class="portal-action-link registry-submit" type="submit">Guardar expediente</button>
      <a class="registry-cancel" href="<?= esc(site_url('app/atletas'), 'attr') ?>">Cancelar</a>
    </div>
  </form>
</div>
<?= $this->endSection() ?>
