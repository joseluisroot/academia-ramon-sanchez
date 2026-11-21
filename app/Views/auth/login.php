<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex,nofollow">
  <meta name="theme-color" content="#071B49">
  <title>Iniciar sesión · Academia Ramón Sánchez</title>
  <link rel="stylesheet" href="<?= esc(base_url('assets/css/academia.css'), 'attr') ?>">
  <link rel="stylesheet" href="<?= esc(base_url('assets/css/portal.css'), 'attr') ?>">
</head>
<body class="auth-page">
<a class="skip-link" href="#contenido">Saltar al contenido</a>
<main id="contenido" class="auth-shell">
  <section class="auth-brand-panel" aria-labelledby="brand-title">
    <a href="<?= esc(site_url('/'), 'attr') ?>" class="auth-brand">
      <span class="brand-mark" aria-hidden="true">RS</span>
      <span><strong>RAMÓN SÁNCHEZ</strong><small>ACADEMIA DE FÚTBOL</small></span>
    </a>
    <div class="auth-brand-content">
      <span class="auth-eyebrow">PLATAFORMA INTEGRAL</span>
      <h1 id="brand-title">Un mismo equipo.<br><em>Una misma visión.</em></h1>
      <p>Información y herramientas para acompañar el desarrollo deportivo y la administración de nuestra academia.</p>
    </div>
    <span class="auth-brand-foot">Acceso exclusivo para usuarios autorizados.</span>
  </section>
  <section class="auth-form-panel" aria-labelledby="login-title">
    <div class="auth-form-wrap">
      <span class="section-kicker">BIENVENIDO</span>
      <h2 id="login-title">Inicia sesión</h2>
      <p class="auth-help">Ingresa con la cuenta que la academia te ha habilitado.</p>

      <?php if (session('error') !== null): ?>
        <div class="auth-alert auth-alert-error" role="alert"><?= esc(session('error')) ?></div>
      <?php elseif (session('errors') !== null): ?>
        <div class="auth-alert auth-alert-error" role="alert">
          <?php foreach ((array) session('errors') as $error): ?>
            <p><?= esc($error) ?></p>
          <?php endforeach ?>
        </div>
      <?php endif ?>
      <?php if (session('message') !== null): ?>
        <div class="auth-alert auth-alert-success" role="status"><?= esc(session('message')) ?></div>
      <?php endif ?>

      <form action="<?= esc(url_to('login'), 'attr') ?>" method="post" class="auth-form">
        <?= csrf_field() ?>
        <label for="email">Correo electrónico</label>
        <input id="email" type="email" name="email" autocomplete="username" inputmode="email" value="<?= esc(old('email') ?? '', 'attr') ?>" required autofocus>

        <label for="password">Contraseña</label>
        <input id="password" type="password" name="password" autocomplete="current-password" required>

        <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
          <label class="auth-remember"><input type="checkbox" name="remember" value="1" <?= old('remember') ? 'checked' : '' ?>> Recordar mi sesión</label>
        <?php endif ?>

        <button class="auth-submit" type="submit">Ingresar al portal <span aria-hidden="true">→</span></button>
      </form>
      <?php if (setting('Auth.allowMagicLinkLogins')): ?>
        <p class="auth-reset">¿No puedes acceder? <a href="<?= esc(url_to('magic-link'), 'attr') ?>">Solicita un enlace de acceso</a></p>
      <?php endif ?>
      <div class="auth-bottom"><a href="<?= esc(site_url('/'), 'attr') ?>">← Volver al sitio institucional</a></div>
    </div>
  </section>
</main>
</body>
</html>
