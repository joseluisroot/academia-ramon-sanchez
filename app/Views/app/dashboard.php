<?= $this->extend('layouts/private') ?>
<?= $this->section('content') ?>

<div class="portal-intro">
  <span class="section-kicker">PLATAFORMA INTEGRAL</span>
  <h1>Bienvenido al portal, <?= esc($username) ?>.</h1>
  <p>Estás accediendo a un espacio privado de la Academia Ramón Sánchez. Los módulos se habilitarán conforme a tus permisos.</p>
</div>

<div class="portal-panel">
  <h2>Tu cuenta</h2>
  <p><strong>Grupos asignados:</strong> <?= esc(implode(', ', $groups ?: ['Sin grupo'])) ?></p>
  <p class="portal-muted">Por seguridad, esta vista no muestra información financiera ni expedientes de atletas. Los módulos de gestión se incorporarán en los siguientes incrementos.</p>
</div>

<?php if (auth()->user()?->can('users.assign')): ?>
  <div class="portal-panel">
    <h2>Administración de accesos</h2>
    <p class="portal-muted">Como responsable designado, puedes asignar roles ordinarios con registro de auditoría.</p>
    <a class="portal-action-link" href="<?= esc(site_url('app/accesos'), 'attr') ?>">Administrar accesos</a>
    <a class="portal-action-link" href="<?= esc(site_url('app/invitaciones'), 'attr') ?>">Invitar usuarios</a>
  </div>
<?php endif ?>

<?php if (auth()->user()?->can('inquiries.view')): ?>
  <div class="portal-panel">
    <h2>Atención a familias</h2>
    <p class="portal-muted">Consulta las solicitudes de información recibidas desde el sitio web y registra las que ya fueron contactadas.</p>
    <a class="portal-action-link" href="<?= esc(site_url('app/consultas'), 'attr') ?>">Ver consultas recibidas</a>
  </div>
<?php endif ?>

<div class="portal-cards">
  <article class="portal-card"><span aria-hidden="true">01</span><h3>Gestión deportiva</h3><p>Entrenamientos, convocatorias y resultados.</p><small>Próximamente</small></article>
  <article class="portal-card"><span aria-hidden="true">02</span><h3>Administración</h3><p>Inscripciones, comprobantes y uniformes.</p><small>Próximamente</small></article>
  <article class="portal-card"><span aria-hidden="true">03</span><h3>Comunicaciones</h3><p>Avisos y atención a familias.</p><small>Próximamente</small></article>
</div>

<?= $this->endSection() ?>
