<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow,noarchive">
    <meta name="referrer" content="no-referrer">
    <meta name="theme-color" content="#071B49">
    <title>Activar cuenta · Academia Ramón Sánchez</title>
    <link rel="stylesheet" href="<?= esc(base_url('assets/css/academia.css'), 'attr') ?>">
    <link rel="stylesheet" href="<?= esc(base_url('assets/css/portal.css'), 'attr') ?>">
</head>
<body class="auth-page">
<main class="activation-shell">
    <div class="activation-card">
        <a class="activation-brand" href="<?= esc(site_url('/'), 'attr') ?>"><img src="<?= esc(base_url('assets/img/brand/escudo-oficial.svg'), 'attr') ?>" width="66" height="66" alt="Escudo oficial de la academia"><span>ACADEMIA RAMÓN SÁNCHEZ</span></a>
        <?php if (! $valid): ?>
            <h1>Esta invitación no está disponible.</h1>
            <p>El enlace es incorrecto, ya fue utilizado o ha vencido. Comunícate con la academia para solicitar uno nuevo.</p>
            <a class="portal-action-link" href="<?= esc(site_url('login'), 'attr') ?>">Volver a iniciar sesión</a>
        <?php else: ?>
            <span class="section-kicker">ACTIVACIÓN DE CUENTA</span>
            <h1>Bienvenido a nuestra academia.</h1>
            <p>Configura tu cuenta personal para ingresar al portal privado.</p>
            <?php if (session('error') !== null): ?>
                <div class="auth-alert auth-alert-error" role="alert"><?= esc(session('error')) ?></div>
            <?php endif ?>
            <form class="invitation-form" method="post" action="<?= esc(site_url('activar-cuenta/' . $token), 'attr') ?>">
                <?= csrf_field() ?>
                <label for="new-username">Nombre de usuario</label>
                <input id="new-username" type="text" name="username" minlength="3" maxlength="30" pattern="[A-Za-z0-9.]+" autocomplete="username" required placeholder="nombre.apellido">
                <label for="new-password">Contraseña</label>
                <input id="new-password" type="password" name="password" minlength="12" maxlength="128" autocomplete="new-password" required>
                <label for="confirm-password">Confirmar contraseña</label>
                <input id="confirm-password" type="password" name="password_confirmation" minlength="12" maxlength="128" autocomplete="new-password" required>
                <p class="portal-muted">Mínimo 12 caracteres, con mayúscula, minúscula, número y símbolo. Evita utilizar tu nombre de usuario.</p>
                <button class="auth-submit" type="submit">Activar cuenta <span aria-hidden="true">→</span></button>
            </form>
        <?php endif ?>
    </div>
</main>
</body>
</html>
