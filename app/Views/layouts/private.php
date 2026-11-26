<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <meta name="theme-color" content="#071B49">
    <link rel="icon" type="image/svg+xml" href="<?= esc(base_url('assets/img/brand/escudo-oficial.svg'), 'attr') ?>">
    <title><?= esc($title ?? 'Portal privado · Academia Ramón Sánchez') ?></title>
    <?php if (is_file(FCPATH . 'assets/css/tailwind.css')): ?><link rel="stylesheet" href="<?= esc(base_url('assets/css/tailwind.css'), 'attr') ?>"><?php endif ?>
    <link rel="stylesheet" href="<?= esc(base_url('assets/css/academia.css'), 'attr') ?>">
    <link rel="stylesheet" href="<?= esc(base_url('assets/css/portal.css'), 'attr') ?>">
    <link rel="stylesheet" href="<?= esc(base_url('assets/css/experience.css'), 'attr') ?>">
</head>
<body class="portal-page">
<a class="skip-link" href="#contenido">Saltar al contenido</a>
<?php
  $portalUser = auth()->user();
  $portalSections = \App\Services\PortalNavigationService::sections($portalUser);
  $currentPortalPath = trim(service('uri')->getPath(), '/');
  $isDashboard = $currentPortalPath === 'app';
  $profileName = 'Mi cuenta';
  if ($portalUser?->inGroup('institutional_admin')) {
      $profileName = 'Administración';
  } elseif ($portalUser?->inGroup('sports_director')) {
      $profileName = 'Dirección deportiva';
  } elseif ($portalUser?->inGroup('coach')) {
      $profileName = 'Entrenador';
  } elseif ($portalUser?->inGroup('equipment_manager')) {
      $profileName = 'Utilero';
  } elseif ($portalUser?->inGroup('guardian')) {
      $profileName = 'Familia';
  } elseif ($portalUser?->inGroup('board_treasurer')) {
      $profileName = 'Tesorería de Junta';
  } elseif ($portalUser?->inGroup('board_member')) {
      $profileName = 'Junta Directiva';
  } elseif ($portalUser?->inGroup('access_manager')) {
      $profileName = 'Gestión de accesos';
  }
  $accountInitial = strtoupper(substr((string) ($portalUser->username ?? 'U'), 0, 1));
?>
<header class="institutional-header">
  <div class="container institutional-topbar">
    <a class="institutional-brand" href="<?= esc(site_url('app'), 'attr') ?>" aria-label="Academia Ramón Sánchez, ir al panel principal">
      <span class="institutional-brand-logo"><img src="<?= esc(base_url('assets/img/brand/escudo-oficial.svg'), 'attr') ?>" alt="" width="48" height="48"></span>
      <span class="institutional-brand-copy">
        <strong>RAMÓN SÁNCHEZ</strong>
        <small>ACADEMIA DE FÚTBOL <span aria-hidden="true">/</span> PLATAFORMA INSTITUCIONAL</small>
      </span>
    </a>
    <div class="institutional-account-actions">
      <span class="institutional-workspace-label">ESPACIO PRIVADO</span>
      <div class="institutional-profile" aria-label="Perfil de acceso">
        <span class="institutional-avatar" aria-hidden="true"><?= esc($accountInitial) ?></span>
        <span class="institutional-profile-copy">
          <strong><?= esc((string) ($portalUser->username ?? 'Usuario')) ?></strong>
          <small><?= esc($profileName) ?></small>
        </span>
      </div>
      <a class="institutional-site-link" href="<?= esc(site_url('/'), 'attr') ?>">Sitio web <span aria-hidden="true">↗</span></a>
      <a class="institutional-exit" href="<?= esc(url_to('logout'), 'attr') ?>">Cerrar sesión</a>
    </div>
  </div>
  <nav class="institutional-navigation" aria-label="Menú principal del portal">
    <div class="container institutional-nav-inner">
      <a class="institutional-nav-home <?= $isDashboard ? 'is-current' : '' ?>" href="<?= esc(site_url('app'), 'attr') ?>" <?= $isDashboard ? 'aria-current="page"' : '' ?>>
        <span class="institutional-nav-home-symbol" aria-hidden="true">▦</span> Panel principal
      </a>
      <?php foreach ($portalSections as $section): ?>
        <?php
          $sectionActive = false;
          foreach ($section['items'] as $item) {
              $itemPath = trim($item['url'], '/');
              if ($currentPortalPath === $itemPath || str_starts_with($currentPortalPath, $itemPath . '/')) {
                  $sectionActive = true;
                  break;
              }
          }
        ?>
        <details class="institutional-nav-group <?= $sectionActive ? 'is-current' : '' ?>">
          <summary aria-label="<?= esc($section['title'], 'attr') ?>">
            <?= esc($section['title']) ?>
            <span class="institutional-nav-caret" aria-hidden="true">⌄</span>
          </summary>
          <div class="institutional-nav-dropdown">
            <span class="institutional-dropdown-heading"><?= esc($section['title']) ?></span>
            <p><?= esc($section['description']) ?></p>
            <?php foreach ($section['items'] as $item): ?>
              <?php
                $itemPath = trim($item['url'], '/');
                $activeItem = $currentPortalPath === $itemPath;
              // Deep pages keep their parent section selected without marking
              // the parent link as the current page.
              if (! $activeItem && $itemPath === 'app/entrenamientos') {
                  $activeItem = preg_match('~^app/entrenamientos/[0-9]+$~', $currentPortalPath) === 1;
              }
              ?>
              <a href="<?= esc(site_url($item['url']), 'attr') ?>" class="<?= $activeItem ? 'is-current' : '' ?>" <?= $activeItem ? 'aria-current="page"' : '' ?>>
                <span><?= esc($item['label']) ?></span><span aria-hidden="true">↗</span>
              </a>
            <?php endforeach ?>
          </div>
        </details>
      <?php endforeach ?>
      <span class="institutional-nav-spacer" aria-hidden="true"></span>
      <span class="institutional-nav-context">ACCESO SEGÚN PERFIL</span>
    </div>
  </nav>
</header>
<main id="contenido" class="container portal-main">
  <?= $this->renderSection('content') ?>
</main>
<footer class="portal-footer"><div class="container">Academia Ramón Sánchez · Acceso institucional</div></footer>
</body>
</html>
