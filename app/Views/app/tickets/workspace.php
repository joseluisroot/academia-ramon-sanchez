<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex,nofollow">
  <meta name="theme-color" content="#071B49">
  <title>ARS Tickets · Academia Ramón Sánchez</title>
  <link rel="stylesheet" href="<?= esc(base_url('assets/css/academia.css'), 'attr') ?>">
  <link rel="stylesheet" href="<?= esc(base_url('assets/css/portal.css'), 'attr') ?>">
  <link rel="stylesheet" href="<?= esc(base_url('assets/css/tickets.css'), 'attr') ?>">
</head>
<body class="portal-page tickets-page">
<a class="skip-link" href="#contenido">Saltar al contenido</a>
<header class="tickets-header">
  <div class="tickets-shell tickets-header-inner">
    <div class="tickets-brand"><img src="<?= esc(base_url('assets/img/brand/escudo-oficial.svg'), 'attr') ?>" width="48" height="48" alt="Escudo de la academia"><div><strong>ARS Tickets</strong><small>ACADEMIA RAMÓN SÁNCHEZ</small></div></div>
    <a class="tickets-exit" href="<?= esc(url_to('logout'), 'attr') ?>">Cerrar sesión</a>
  </div>
</header>
<main id="contenido" class="tickets-shell tickets-main">
  <section class="tickets-hero">
    <span class="tickets-kicker">GESTIÓN DE EVENTOS · TKT-001</span>
    <h1>Bienvenido, <?= esc($username) ?></h1>
    <p>Espacio de trabajo para la gestión segura de entradas y control de accesos.</p>
  </section>
  <section class="tickets-notice" aria-label="Estado del sistema">
    <h2>Fundamentos habilitados</h2>
    <p>Tu acceso está autorizado. La creación de eventos, emisión de entradas y validación QR aún no están disponibles.</p>
    <p>No se pueden emitir ni consumir entradas en este incremento.</p>
  </section>
  <a href="<?= esc(site_url('/'), 'attr') ?>">Volver al sitio institucional</a>
</main>
</body>
</html>
