<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow,noarchive">
    <meta name="theme-color" content="#071B49">
    <title><?= esc((string) $statusCode) ?> · Academia Ramón Sánchez</title>
    <link rel="icon" type="image/svg+xml" href="<?= esc(base_url('assets/img/brand/escudo-oficial.svg'), 'attr') ?>">
    <link rel="stylesheet" href="<?= esc(base_url('assets/css/academia.css'), 'attr') ?>">
    <link rel="stylesheet" href="<?= esc(base_url('assets/css/portal.css'), 'attr') ?>">
</head>
<body class="institutional-error-page">
    <a class="skip-link" href="#contenido">Saltar al contenido</a>
    <header class="institutional-error-header">
        <div class="container">
            <a class="brand" href="<?= esc(site_url('/'), 'attr') ?>" aria-label="Academia Ramón Sánchez, volver al inicio">
                <img class="brand-logo" src="<?= esc(base_url('assets/img/brand/escudo-oficial.svg'), 'attr') ?>" alt="Escudo oficial de la Academia Ramón Sánchez" width="54" height="54">
                <span class="brand-copy"><strong>RAMÓN SÁNCHEZ</strong><small>ACADEMIA INTEGRAL DE FÚTBOL</small></span>
            </a>
        </div>
    </header>
    <main id="contenido" class="container institutional-error-main">
        <div class="institutional-error-panel">
            <img class="institutional-error-illustration"
                src="<?= esc(base_url('assets/img/illustrations/' . $illustration), 'attr') ?>"
                alt="<?= esc($illustrationAlt, 'attr') ?>" width="480" height="320">
            <div class="institutional-error-copy">
                <span class="section-kicker"><?= esc($eyebrow) ?></span>
                <span class="institutional-error-number"><?= esc((string) $statusCode) ?></span>
                <h1><?= esc($title) ?></h1>
                <p><?= esc($message) ?></p>
                <div class="institutional-error-actions">
                    <?php if (function_exists('auth') && auth()->loggedIn()): ?>
                        <a class="btn institutional-error-primary" href="<?= esc(site_url('app'), 'attr') ?>">Volver al panel</a>
                    <?php endif ?>
                    <a class="btn institutional-error-secondary" href="<?= esc(site_url('/'), 'attr') ?>">Ir al sitio principal</a>
                </div>
            </div>
        </div>
    </main>
    <footer class="institutional-error-footer">
        <div class="container">Academia Integral de Fútbol Profesor Ramón Sánchez · Formación, respeto y juego limpio</div>
    </footer>
</body>
</html>
