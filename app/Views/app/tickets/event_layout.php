<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>Eventos · ARS Tickets</title>
<link rel="stylesheet" href="<?= esc(base_url('assets/css/tickets.css'),'attr') ?>">
</head><body class="tickets-page"><header class="tickets-header"><div class="tickets-shell tickets-header-inner"><div class="tickets-brand"><img src="<?= esc(base_url('assets/img/brand/escudo-oficial.svg'),'attr') ?>" width="48" height="48" alt="Escudo"><strong>ARS Tickets</strong></div><a class="tickets-exit" href="<?= esc(site_url('tickets'),'attr') ?>">Panel</a></div></header>
<main class="tickets-shell tickets-main"><a href="<?= esc(site_url('tickets/eventos'),'attr') ?>">Todos los eventos</a>
<?php foreach (['message','error'] as $key): if (session($key)): ?><p class="tickets-notice" role="<?= $key==='error'?'alert':'status' ?>"><?= esc(session($key)) ?></p><?php endif; endforeach ?>
<?= $this->renderSection('content') ?></main></body></html>
