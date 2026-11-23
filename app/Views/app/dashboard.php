<?= $this->extend('layouts/private') ?>
<?= $this->section('content') ?>

<div class="executive-dashboard">
  <section class="executive-hero" aria-labelledby="executive-heading">
    <div class="executive-hero-copy">
      <span class="executive-eyebrow"><span class="executive-pulse" aria-hidden="true"></span> ACADEMIA RAMÓN SÁNCHEZ · PORTAL PRIVADO</span>
      <div class="executive-greeting">Tu espacio de gestión</div>
      <h1 id="executive-heading"><?= esc($overview['role']) ?></h1>
      <p><?= esc($overview['subtitle']) ?></p>
      <div class="executive-hero-actions">
        <?php if ($overview['next'] !== null): ?>
          <a class="executive-button executive-button-primary" href="<?= esc(site_url($overview['next']['url']), 'attr') ?>"><?= esc($overview['next']['label']) ?> <span aria-hidden="true">↗</span></a>
        <?php endif ?>
        <a class="executive-button executive-button-outline" href="#modulos">Explorar mis herramientas <span aria-hidden="true">↓</span></a>
      </div>
    </div>
    <div class="executive-hero-emblem" aria-hidden="true">
      <img src="<?= esc(base_url('assets/img/brand/escudo-oficial.svg'), 'attr') ?>" alt="" width="170" height="170">
      <span>DISCIPLINA · FORMACIÓN · EQUIPO</span>
    </div>
  </section>

  <div class="executive-section-heading">
    <div>
      <span class="executive-label">RESUMEN OPERATIVO</span>
      <h2>Panorama de tu actividad</h2>
    </div>
    <span class="executive-privacy"><span aria-hidden="true">●</span> Datos según tu perfil</span>
  </div>

  <?php if ($overview['metrics'] !== []): ?>
    <div class="executive-metrics">
      <?php foreach ($overview['metrics'] as $metric): ?>
        <article class="executive-metric">
          <span class="executive-metric-label"><?= esc($metric['label']) ?></span>
          <strong class="executive-metric-value"><?= esc($metric['value']) ?></strong>
          <small><?= esc($metric['note']) ?></small>
        </article>
      <?php endforeach ?>
    </div>
  <?php else: ?>
    <div class="executive-empty-overview">
      <div class="executive-empty-icon" aria-hidden="true">◎</div>
      <div>
        <strong>Un panel diseñado para tus funciones</strong>
        <p>Los indicadores de tu área aparecerán conforme habilitemos sus módulos. Por ahora, accede directamente a tus herramientas autorizadas.</p>
      </div>
    </div>
  <?php endif ?>

  <div class="executive-section-heading" id="modulos">
    <div>
      <span class="executive-label">ACCESOS DE TRABAJO</span>
      <h2>Tus herramientas</h2>
    </div>
    <span class="executive-section-count"><?= count($sections) ?> <?= count($sections) === 1 ? 'área disponible' : 'áreas disponibles' ?></span>
  </div>

  <?php if ($sections !== []): ?>
    <div class="executive-modules">
      <?php foreach ($sections as $index => $section): ?>
        <article class="executive-module">
          <div class="executive-module-header">
            <span class="executive-module-number"><?= esc(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) ?></span>
            <span class="executive-module-status">Habilitado</span>
          </div>
          <h3><?= esc($section['title']) ?></h3>
          <p><?= esc($section['description']) ?></p>
          <div class="executive-module-links">
            <?php foreach ($section['items'] as $item): ?>
              <a href="<?= esc(site_url($item['url']), 'attr') ?>"><span><?= esc($item['label']) ?></span><span aria-hidden="true">↗</span></a>
            <?php endforeach ?>
          </div>
        </article>
      <?php endforeach ?>
    </div>
  <?php else: ?>
    <div class="executive-empty-overview">
      <div class="executive-empty-icon" aria-hidden="true">◌</div>
      <div>
        <strong>Aún no hay herramientas operativas habilitadas</strong>
        <p>Tu cuenta tiene acceso al portal. La administración podrá ayudarte a revisar qué funciones corresponden a tu perfil.</p>
      </div>
    </div>
  <?php endif ?>

  <aside class="executive-footnote">
    <div>
      <strong>Una plataforma para cada función de la academia.</strong>
      <p>Los módulos se incorporan progresivamente. Tu panel muestra únicamente las opciones actualmente disponibles para tu cuenta.</p>
    </div>
    <span class="executive-footnote-mark" aria-hidden="true">RS</span>
  </aside>
</div>

<?= $this->endSection() ?>
