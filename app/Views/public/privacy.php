<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>

<section class="section section-light">
  <div class="container legal-page">
    <span class="section-kicker">TRATAMIENTO DE CONSULTAS</span>
    <h1>Privacidad del formulario de contacto</h1>
    <p>La Academia Integral de Fútbol Profesor Ramón Sánchez utiliza los datos que el adulto responsable envía desde el formulario público únicamente para atender consultas sobre nuestros entrenamientos y el proceso de ingreso.</p>

    <h2>¿Qué información solicitamos?</h2>
    <p>Nombre y correo electrónico del adulto, teléfono opcional, categoría de interés y mensaje opcional. No solicitamos nombres, documentos, fotografías ni datos de salud de niños o jóvenes en esta etapa.</p>

    <h2>¿Quién puede consultar estos datos?</h2>
    <p>Personal de la academia expresamente autorizado para atender nuevas solicitudes. Los mensajes no se publican ni se incluyen en perfiles de atletas.</p>

    <h2>¿Para qué los utilizamos?</h2>
    <p>Para responder a tu consulta y, si corresponde, coordinar información sobre una primera clase de prueba. Enviar este formulario no crea una cuenta en el portal ni constituye inscripción del atleta.</p>

    <h2>Consultas sobre tus datos</h2>
    <p>Podrás solicitar una revisión o eliminación de los datos de tu consulta a través de la administración de la academia. El procedimiento formal de retención y eliminación se documentará antes de la puesta en producción.</p>

    <p><a class="btn legal-back" href="<?= esc(site_url('/'), 'attr') ?>">Volver al inicio</a></p>
  </div>
</section>
<?= $this->endSection() ?>
