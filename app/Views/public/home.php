<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>

<section class="hero">
    <div class="container hero-grid">
        <div class="hero-content">
            <span class="eyebrow"><span class="eyebrow-dot" aria-hidden="true"></span> ACADEMIA DE FÚTBOL RAMÓN SÁNCHEZ</span>
            <h1>Formamos futbolistas.<br><em>Impulsamos valores.</em></h1>
            <p>Un espacio para desarrollar disciplina, compañerismo y pasión por el fútbol, acompañando a cada atleta en su crecimiento.</p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="#contacto">Conoce la primera clase <span aria-hidden="true">↗</span></a>
                <a class="btn btn-outline" href="#academia">Conoce la academia</a>
            </div>
            <div class="hero-tags" aria-label="Valores formativos">
                <span>Disciplina</span><span>Respeto</span><span>Trabajo en equipo</span>
            </div>
        </div>
        <div class="hero-visual" aria-hidden="true">
            <div class="hero-orbit orbit-one"></div>
            <div class="hero-orbit orbit-two"></div>
            <div class="sport-symbol">⚽</div>
            <div class="floating-note"><strong>5 categorías</strong><small>U7 · U9 · U11 · U13 · U17</small></div>
        </div>
    </div>
</section>

<section id="academia" class="section section-light">
    <div class="container section-grid">
        <div>
            <span class="section-kicker">NUESTRA ACADEMIA</span>
            <h2>Un proyecto deportivo con identidad.</h2>
        </div>
        <div class="body-copy">
            <p>La Academia Ramón Sánchez acompaña a niños y jóvenes en su formación futbolística. El nuevo portal reunirá información institucional, comunicación con familias y seguimiento deportivo.</p>
            <p class="muted">Esta es la primera versión visual del sitio. La historia oficial y las fotografías institucionales se incorporarán tras su revisión y autorización.</p>
        </div>
    </div>
</section>

<section id="categorias" class="section">
    <div class="container">
        <div class="section-head">
            <div><span class="section-kicker">FORMACIÓN POR EDADES</span><h2>Nuestras categorías</h2></div>
            <p>Un recorrido deportivo adaptado a cada etapa.</p>
        </div>
        <div class="categories-grid">
            <?php foreach (['U7', 'U9', 'U11', 'U13', 'U17'] as $category): ?>
                <article class="category-card">
                    <span class="category-symbol" aria-hidden="true">↗</span>
                    <h3><?= esc($category) ?></h3>
                    <p>Formación y desarrollo deportivo.</p>
                </article>
            <?php endforeach ?>
        </div>
    </div>
</section>

<section id="beneficios" class="section section-navy">
    <div class="container">
        <span class="section-kicker">MÁS QUE FÚTBOL</span>
        <h2>Una formación que trasciende la cancha.</h2>
        <div class="values-grid">
            <article><span class="value-icon" aria-hidden="true">01</span><h3>Disciplina</h3><p>Hábitos de constancia, preparación y responsabilidad.</p></article>
            <article><span class="value-icon" aria-hidden="true">02</span><h3>Compañerismo</h3><p>Respeto y trabajo conjunto en cada entrenamiento.</p></article>
            <article><span class="value-icon" aria-hidden="true">03</span><h3>Desarrollo</h3><p>Seguimiento de la evolución y reconocimiento del esfuerzo.</p></article>
        </div>
    </div>
</section>

<section id="contacto" class="section">
    <div class="container contact-panel">
        <div>
            <span class="section-kicker">DA EL PRIMER PASO</span>
            <h2>Conoce la academia.</h2>
            <p>La primera clase de prueba es gratuita. Dennis Sánchez orienta a las familias sobre el funcionamiento y las categorías.</p>
        </div>
        <div class="contact-details">
            <strong>Entrenamientos habituales</strong>
            <p>Miércoles y jueves · 4:30 a 6:00 p. m.</p>
            <p class="muted">Próximamente se habilitará aquí el canal de consultas y solicitud de clase gratuita.</p>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
