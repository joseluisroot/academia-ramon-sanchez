<?= $this->extend('app/tickets/event_layout') ?>
<?= $this->section('content') ?>
<?php
$url=site_url('tickets/eventos/'.$event['id']);
$draft=$event['status']==='DRAFT';
$summary=\App\Services\Tickets\EventPolicy::summary((int)$event['capacity'],$types);
$statuses=['DRAFT'=>'Borrador','OPEN'=>'Abierto','CLOSED'=>'Cerrado','CANCELLED'=>'Cancelado'];
?>
<section class="tickets-hero"><span class="tickets-kicker">GESTIÓN DE EVENTOS</span><h1><?= esc($event['name']) ?></h1><p><?= esc($event['venue']) ?> · <?= esc(\App\Services\Tickets\EventPolicy::local($event['starts_at'])) ?> · <?= esc($statuses[$event['status']]) ?></p></section>
<div class="tickets-metrics">
<?php foreach (['Capacidad total'=>$event['capacity'],'Personas asignadas'=>$summary['assigned'],'Personas disponibles'=>$summary['available'],'Tarjetas configuradas'=>$summary['cards'],'Ingreso potencial (no recaudado)'=>'$'.number_format($summary['cents']/100,2),'Pliegos láser estimados'=>$summary['sheets']] as $label=>$value): ?>
<div><small><?= esc($label) ?></small><strong><?= esc((string)$value) ?></strong></div>
<?php endforeach ?>
</div>
<section class="tickets-notice"><h2>Configuración</h2><p>Plantilla: <?= esc($event['print_template']) ?> · 4 × 6 / 12 × 18 pulgadas.</p>
<?php if ($draft): ?><form class="tickets-form" method="post" action="<?= esc($url,'attr') ?>"><?= csrf_field() ?><?= view('app/tickets/event_fields',['event'=>$event]) ?><button>Guardar cambios</button></form><?php endif ?>
</section>
<section class="tickets-notice"><h2>Tipos de entradas</h2>
<?php foreach ($types as $type): ?>
<article class="tickets-type"><h3><?= esc($type['name']) ?></h3><p>$<?= esc($type['price']) ?> · <?= esc((string)$type['quota']) ?> tarjetas · <?= esc((string)$type['adult_qty']) ?> adultos + <?= esc((string)$type['student_qty']) ?> alumnos por tarjeta</p>
<?php if ($draft): ?><details><summary>Editar tipo de entrada</summary><form class="tickets-form" method="post" action="<?= esc($url.'/tipos/'.$type['id'],'attr') ?>"><?= csrf_field() ?><?= view('app/tickets/type_fields',['type'=>$type]) ?><button>Guardar tipo</button></form></details>
<form method="post" data-confirm="¿Eliminar este tipo de entrada?" action="<?= esc($url.'/tipos/'.$type['id'].'/eliminar','attr') ?>"><?= csrf_field() ?><button class="tickets-danger">Eliminar tipo</button></form><?php endif ?>
</article>
<?php endforeach ?>
<?php if ($draft): ?><h3>Agregar tipo</h3><form class="tickets-form" method="post" action="<?= esc($url.'/tipos','attr') ?>"><?= csrf_field() ?><?= view('app/tickets/type_fields',['type'=>[]]) ?><button>Agregar tipo</button></form><?php endif ?>
</section>
<?php if (in_array($event['status'],['DRAFT','OPEN'],true)): ?><section class="tickets-notice"><h2>Cambiar estado</h2><p>Al abrir el evento se bloquea la edición. Cerrar y cancelar son definitivos.</p><form class="tickets-form" method="post" data-confirm="¿Confirmas el cambio de estado seleccionado?" action="<?= esc($url.'/estado','attr') ?>"><?= csrf_field() ?><label>Nuevo estado <select name="status"><option value="<?= $draft?'OPEN':'CLOSED' ?>"><?= $draft?'Abrir':'Cerrar' ?></option><option value="CANCELLED">Cancelar</option></select></label><label class="tickets-check"><input type="checkbox" name="confirmed" value="yes" required> Comprendo las consecuencias y confirmo el cambio.</label><button>Confirmar estado</button></form></section><?php endif ?>
<?= $this->endSection() ?>
