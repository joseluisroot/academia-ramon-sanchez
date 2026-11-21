<?= $this->extend('layouts/private') ?>
<?= $this->section('content') ?>
<div class="portal-intro">
  <span class="section-kicker">PROTECCIÓN DE MENORES</span>
  <h1>Verificar vínculos familiares</h1>
  <p>Únicamente una autoridad institucional puede autorizar qué cuenta de responsable accede a los datos de un atleta.</p>
</div>
<?php if (session('message')): ?>
  <div class="auth-alert auth-alert-success" role="status"><?= esc(session('message')) ?></div>
<?php endif ?>
<?php if (session('error')): ?>
  <div class="auth-alert auth-alert-error" role="alert"><?= esc(session('error')) ?></div>
<?php endif ?>
<div class="portal-panel">
  <h2>Pendientes de revisión</h2>
  <p class="portal-muted">Antes de aprobar, verifica la identidad de la persona y su relación legal o autorización correspondiente fuera de la plataforma. No basta con conocer el correo o compartir apellido.</p>
  <?php foreach ($links as $link): ?>
    <section class="family-verification-item" aria-label="Relación familiar pendiente">
      <div>
        <strong>Atleta: <?= esc($link['athlete_first'] . ' ' . $link['athlete_last']) ?></strong>
        <p>Adulto registrado: <?= esc($link['adult_first'] . ' ' . $link['adult_last']) ?></p>
        <small>Relación registrada: <?= esc($link['relationship']) ?> · Referencia #<?= (int) $link['id'] ?></small>
      </div>
      <form class="family-verify-form" method="post" action="<?= esc(site_url('app/familias/verificar'), 'attr') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="relation_id" value="<?= (int) $link['id'] ?>">
        <label>Correo de la cuenta de responsable activada
          <input type="email" name="email" maxlength="254" autocomplete="off" required placeholder="responsable@example.com">
        </label>
        <label class="family-check"><input type="checkbox" name="verified_relationship" value="1" required><span>Confirmo que comprobé la identidad de la persona y su vínculo con este menor mediante el proceso institucional.</span></label>
        <button type="submit" class="portal-action-link registry-submit">Autorizar vínculo</button>
      </form>
    </section>
  <?php endforeach ?>
  <?php if ($links === []): ?>
    <p>No hay relaciones pendientes de verificación.</p>
  <?php endif ?>
</div>
<?= $this->endSection() ?>
