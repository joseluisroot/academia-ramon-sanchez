<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <meta name="theme-color" content="#071B49">
    <title><?= esc($title ?? 'Portal privado · Academia Ramón Sánchez') ?></title>
    <link rel="stylesheet" href="<?= esc(base_url('assets/css/academia.css'), 'attr') ?>">
    <link rel="stylesheet" href="<?= esc(base_url('assets/css/portal.css'), 'attr') ?>">
</head>
<body class="portal-page">
<a class="skip-link" href="#contenido">Saltar al contenido</a>
<header class="portal-header">
  <div class="container portal-header-inner">
    <a class="portal-brand" href="<?= esc(site_url('/app'), 'attr') ?>">
      <span class="brand-mark" aria-hidden="true">RS</span>
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
