<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <meta name="theme-color" content="#071B49">
    <link rel="icon" type="image/svg+xml" href="<?= esc(base_url('assets/img/brand/escudo-oficial.svg'), 'attr') ?>">
    <title><?= esc($title ?? 'Portal privado · Academia Ramón Sánchez') ?></title>
    <link rel="stylesheet" href="<?= esc(base_url('assets/css/academia.css'), 'attr') ?>">
    <link rel="stylesheet" href="<?= esc(base_url('assets/css/portal.css'), 'attr') ?>">
</head>
<body class="portal-page">
<a class="skip-link" href="#contenido">Saltar al contenido</a>
<header class="portal-header">
  <div class="container portal-header-inner">
    <a class="portal-brand" href="<?= esc(site_url('/app'), 'attr') ?>">
      <img class="brand-logo" src="<?= esc(base_url('assets/img/brand/escudo-oficial.svg'), 'attr') ?>" alt="Escudo oficial de la Academia Ramón Sánchez" width="54" height="54">
      <span><strong>RAMÓN SÁNCHEZ</strong><small>PORTAL PRIVADO</small></span>
    </a>
    <div class="portal-header-actions">
      <a href="<?= esc(site_url('/'), 'attr') ?>">Ver sitio público</a>
      <a class="portal-logout" href="<?= esc(url_to('logout'), 'attr') ?>">Cerrar sesión</a>
    </div>
  </div>
</header>
<main id="contenido" class="container portal-main">
  <?= $this->renderSection('content') ?>
</main>
<footer class="portal-footer"><div class="container">Academia Ramón Sánchez · Acceso institucional</div></footer>
</body>
</html>
