<?= $this->extend('layouts/private') ?>
<?= $this->section('content') ?>
<div class="portal-intro">
  <span class="section-kicker">PORTAL DE FAMILIAS</span>
  <h1>Mis atletas</h1>
  <p>Un espacio para consultar únicamente la información deportiva autorizada de tu familia.</p>
</div>
<?php if ($athletes === []): ?>
  <div class="portal-panel family-empty">
    <h2>Tu equipo familiar se está preparando.</h2>
    <p>Por ahora no encontramos atletas vinculados y verificados con tu cuenta. La academia debe confirmar el vínculo con el adulto responsable antes de habilitar información de un menor.</p>
    <p class="portal-muted">Si ya formas parte de la academia, comunícate con la administración para verificar tu registro.</p>
  </div>
<?php else: ?>
  <div class="family-athlete-grid">
    <?php foreach ($athletes as $athlete): ?>
      <article class="portal-panel family-athlete-card">
        <span class="section-kicker">CATEGORÍA <?= esc($athlete['category_code']) ?></span>
        <h2><?= esc($athlete['first_name'] . ' ' . $athlete['last_name']) ?></h2>
        <p>Expediente deportivo: activo</p>
        <small>Las funciones de asistencia y partidos estarán disponibles en los próximos incrementos.</small>
      </article>
    <?php endforeach ?>
  </div>
<?php endif ?>
<?= $this->endSection() ?>
