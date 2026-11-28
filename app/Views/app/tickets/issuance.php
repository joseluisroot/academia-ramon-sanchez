<?= $this->extend('app/tickets/event_layout') ?>
<?= $this->section('content') ?>
<?php $url=site_url('tickets/eventos/'.$event['id']); ?>
<p><a href="<?= esc($url,'attr') ?>">Volver al evento</a></p>
<section class="tickets-hero"><span class="tickets-kicker">EMISIÓN · TKT-003.1</span><h1><?= esc($event['name']) ?></h1><p>Generación segura de lotes. No habilita ventas, impresión ni ingreso.</p></section>
<div class="tickets-metrics"><?php foreach ($types as $type): ?><div><small><?= esc($type['name']) ?></small><strong><?= esc((string)$type['available']) ?> disponibles</strong><p><?= esc((string)$type['issued']) ?> emitidas / <?= esc((string)$type['quota']) ?> cuota</p></div><?php endforeach ?></div>
<section class="tickets-notice"><h2>Emitir lote</h2>
<?php if (!$ready): ?><p role="alert">Configura la clave de emisión en el entorno privado antes de continuar.</p><?php elseif ($event['status']!=='OPEN'): ?><p>El evento debe estar abierto para emitir entradas.</p><?php elseif (!array_filter($types,static fn($t)=>$t['available']>0)): ?><p>No hay cuota disponible.</p><?php else: ?>
<form class="tickets-form" method="post" action="<?= esc($url.'/emision','attr') ?>" data-confirm="¿Confirmas la emisión del lote? Los correlativos no se reutilizan.">
<?= csrf_field() ?><input type="hidden" name="request_id" value="<?= esc($requestId,'attr') ?>">
<label>Tipo de entrada <select name="type_id" required><?php foreach ($types as $type): if ($type['available']>0): ?><option value="<?= esc((string)$type['id'],'attr') ?>"><?= esc($type['name']) ?> — <?= esc((string)$type['available']) ?> disponibles</option><?php endif;endforeach ?></select></label>
<label>Cantidad de tarjetas <input type="number" name="quantity" min="1" max="500" required></label>
<label class="tickets-check"><input type="checkbox" name="confirmed" value="yes" required> Confirmo la emisión definitiva de estos correlativos.</label><button>Generar lote</button></form><?php endif ?></section>
<section class="tickets-notice"><h2>Últimos lotes (hasta 100)</h2><?php if (!$batches): ?><p>Todavía no hay lotes.</p><?php endif ?>
<?php foreach ($batches as $batch): ?><article class="tickets-type"><h3>Lote #<?= esc((string)$batch['id']) ?> · <?= esc($batch['name']) ?></h3><p><?= esc((string)$batch['quantity']) ?> tarjetas · N.º <?= esc(str_pad((string)$batch['first_serial'],6,'0',STR_PAD_LEFT)) ?> a <?= esc(str_pad((string)$batch['last_serial'],6,'0',STR_PAD_LEFT)) ?></p><small><?= esc(\App\Services\Tickets\EventPolicy::local($batch['created_at'])) ?> (hora local)</small></article><?php endforeach ?></section>
<?= $this->endSection() ?>
