<?= $this->extend('app/tickets/event_layout') ?>
<?= $this->section('content') ?>
<h1>Eventos</h1>
<section class="tickets-notice"><h2>Eventos registrados</h2>
<?php if (!$events): ?><p>No hay eventos registrados.</p><?php endif ?>
<?php foreach ($events as $item): ?><p><a href="<?= esc(site_url('tickets/eventos/'.$item['id']),'attr') ?>"><?= esc($item['name']) ?></a> · <?= esc($item['status']) ?> · <?= esc(\App\Services\Tickets\EventPolicy::local($item['starts_at'])) ?></p><?php endforeach ?>
</section>
<section class="tickets-notice"><h2>Crear evento</h2><form class="tickets-form" method="post" action="<?= esc(site_url('tickets/eventos'),'attr') ?>"><?= csrf_field() ?><?= view('app/tickets/event_fields') ?><button>Crear borrador</button></form></section>
<?= $this->endSection() ?>
