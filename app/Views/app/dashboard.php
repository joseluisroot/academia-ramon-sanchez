<?= $this->extend('layouts/private') ?>
<?= $this->section('content') ?>

<div class="portal-intro">
  <span class="section-kicker">PLATAFORMA INTEGRAL · ACCESO PERSONALIZADO</span>
  <h1>Bienvenido al portal, <?= esc($username) ?>.</h1>
  <p>Selecciona una función disponible para tu perfil. Solo se muestran opciones que tu cuenta puede utilizar.</p>
</div>

<div class="portal-panel">
  <h2>Tu espacio de trabajo</h2>
  <p class="portal-muted">Perfiles activos: <?= esc(implode(', ', $groups ?: ['Sin grupo'])) ?>. Los permisos para cada operación se comprueban también en el servidor.</p>
</div>

<?php if ($sections !== []): ?>
  <div class="portal-cards">
    <?php foreach ($sections as $section): ?>
      <article class="portal-card">
        <h2><?= esc($section['title']) ?></h2>
        <p><?= esc($section['description']) ?></p>
        <div class="registry-actions">
          <?php foreach ($section['items'] as $item): ?>
            <a class="portal-action-link" href="<?= esc(site_url($item['url']), 'attr') ?>"><?= esc($item['label']) ?></a>
          <?php endforeach ?>
        </div>
      </article>
    <?php endforeach ?>
  </div>
<?php else: ?>
  <div class="portal-panel">
    <h2>Sin módulos operativos habilitados</h2>
    <p class="portal-muted">Tu cuenta tiene acceso al portal, pero todavía no tiene funciones operativas disponibles. Solicita a la administración que revise tus permisos.</p>
  </div>
<?php endif ?>

<div class="portal-panel">
  <h2>Módulos en construcción</h2>
  <p class="portal-muted">Convocatorias, torneos, finanzas, uniformes, comunicaciones y notificaciones aparecerán en tu panel únicamente cuando se implementen y tu cuenta cuente con el permiso correspondiente.</p>
</div>

<?= $this->endSection() ?>
