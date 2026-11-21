<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Academia Integral de Fútbol Profesor Ramón Sánchez, en Sitio del Niño, San Juan Opico. Formación integral de niños y jóvenes mediante el fútbol, los valores y el juego limpio.">
    <meta name="theme-color" content="#071B49">
    <link rel="icon" type="image/svg+xml" href="<?= esc(base_url('assets/img/brand/escudo-oficial.svg'), 'attr') ?>">
    <title><?= esc($title ?? 'Academia Ramón Sánchez') ?></title>
    <link rel="stylesheet" href="<?= esc(base_url('assets/css/academia.css'), 'attr') ?>">
</head>
<body>
<a class="skip-link" href="#contenido">Saltar al contenido</a>
<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="<?= esc(site_url('/'), 'attr') ?>" aria-label="Academia Ramón Sánchez, inicio">
            <img class="brand-logo" src="<?= esc(base_url('assets/img/brand/escudo-oficial.svg'), 'attr') ?>" alt="Escudo oficial de la Academia Ramón Sánchez" width="54" height="54">
            <span class="brand-copy"><strong>RAMÓN SÁNCHEZ</strong><small>ACADEMIA DE FÚTBOL</small></span>
        </a>
        <nav class="main-nav" aria-label="Navegación principal">
            <a href="#academia">La academia</a>
            <a href="#mision-vision">Misión y visión</a>
            <a href="#categorias">Categorías</a>
            <a href="#beneficios">Formación</a>
            <a class="nav-login" href="<?= esc(site_url('login'), 'attr') ?>">Ingresar</a>
            <a class="nav-cta" href="#contacto">Primera clase gratuita</a>
        </nav>
    </div>
</header>
<main id="contenido">
    <?= $this->renderSection('content') ?>
</main>
<footer class="site-footer">
    <div class="container footer-content">
        <div class="footer-brand"><img class="footer-logo" src="<?= esc(base_url('assets/img/brand/escudo-oficial.svg'), 'attr') ?>" alt="Escudo oficial" width="52" height="52"><p><strong>Academia de Fútbol Ramón Sánchez</strong><br><span>Formación deportiva y valores.</span></p></div>
        <p>San Juan Opico, La Libertad · © <?= date('Y') ?> Academia Ramón Sánchez.</p>
    </div>
</footer>
</body>
</html>
