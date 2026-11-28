<?= $this->extend('app/tickets/event_layout') ?>
<?= $this->section('content') ?>
<h1><?= esc($event['name']) ?></h1><p>Estado: <?= esc($event['status']) ?> · Capacidad: <?= esc((string)$event['capacity']) ?> personas</p>
<?php $url=site_url('tickets/eventos/'.$event['id']); ?>
<section class="tickets-notice"><h2>Configuración</h2>
<p>Plantilla: <?= esc($event['print_template']) ?> · 4 × 6 pulgadas / pliego 12 × 18 pulgadas (impresión pendiente TKT-003).</p>
<?php if ($event['status']==='DRAFT'): ?><form class="tickets-form" method="post" action="<?= esc($url,'attr') ?>"><?= csrf_field() ?><?= view('app/tickets/event_fields',['event'=>$event]) ?><button>Guardar cambios</button></form><?php else: ?><p><?= esc($event['venue']) ?> · <?= esc(\App\Services\Tickets\EventPolicy::local($event['starts_at'])) ?></p><?php endif ?>
</section>
<section class="tickets-notice"><h2>Tipos de entradas</h2>
<?php foreach ($types as $type): ?><p><?= esc($type['name']) ?> · $<?= esc($type['price']) ?> · <?= esc((string)$type['quota']) ?> tarjetas · <?= esc((string)$type['adult_qty']) ?> adultos + <?= esc((string)$type['student_qty']) ?> alumnos por tarjeta</p><?php endforeach ?>
<?php if ($event['status']==='DRAFT'): ?><form class="tickets-form" method="post" action="<?= esc($url.'/tipos','attr') ?>"><?= csrf_field() ?>
<label>Nombre <input name="name" required maxlength="100"></label><label>Precio USD <input name="price" type="number" min="0" step="0.01" required></label><label>Adultos <input name="adult_qty" type="number" min="0" max="100" required></label><label>Alumnos <input name="student_qty" type="number" min="0" max="100" required></label><label>Cuota de tarjetas <input name="quota" type="number" min="1" required></label><button>Agregar tipo</button></form><?php endif ?>
</section>
<?php if (in_array($event['status'],['DRAFT','OPEN'],true)): ?><section class="tickets-notice"><h2>Cambiar estado</h2><p>Los eventos abiertos no se pueden editar; cierre y cancelación son definitivos en esta versión.</p><form class="tickets-form" method="post" action="<?= esc($url.'/estado','attr') ?>"><?= csrf_field() ?><label>Nuevo estado <select name="status"><option value="<?= $event['status']==='DRAFT'?'OPEN':'CLOSED' ?>"><?= $event['status']==='DRAFT'?'Abrir':'Cerrar' ?></option><option value="CANCELLED">Cancelar</option></select></label><button>Confirmar estado</button></form></section><?php endif ?>
<?= $this->endSection() ?>
