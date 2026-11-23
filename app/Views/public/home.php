<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>

<section class="hero editorial-hero">
    <div class="container hero-grid editorial-hero-grid">
        <div class="hero-content">
            <span class="eyebrow"><span class="eyebrow-dot" aria-hidden="true"></span> ACADEMIA INTEGRAL DE FÚTBOL PROFESOR RAMÓN SÁNCHEZ</span>
            <h1>El futuro se forma<br><em>jugando juntos.</em></h1>
            <p>Formación futbolística y humana desde Sitio del Niño. Un espacio para aprender, competir con respeto y crecer como equipo.</p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="#contacto">Conoce nuestra academia <span aria-hidden="true">↗</span></a>
                <a class="btn btn-outline" href="#academia">Nuestra historia</a>
            </div>
            <div class="hero-tags" aria-label="Nuestros valores">
                <span>Disciplina</span><span>Compañerismo</span><span>Juego limpio</span>
            </div>
        </div>
        <div class="editorial-hero-art">
            <?php if ($publicPhotos !== []): ?>
                <img class="editorial-hero-photo" src="<?= esc($publicPhotos[0]['src'], 'attr') ?>" alt="<?= esc($publicPhotos[0]['alt'], 'attr') ?>" width="900" height="670" fetchpriority="high" decoding="async">
            <?php else: ?>
                <div class="editorial-hero-brand" aria-hidden="true">
                    <img src="<?= esc(base_url('assets/img/brand/escudo-oficial.svg'), 'attr') ?>" alt="" width="205" height="205">
                    <span>FORMACIÓN · RESPETO · EQUIPO</span>
                </div>
            <?php endif ?>
            <div class="editorial-hero-note"><span class="editorial-dot" aria-hidden="true"></span> COMPROMETIDOS CON EL FÚTBOL FORMATIVO</div>
        </div>
    </div>
</section>

<section id="academia" class="section section-light">
    <div class="container section-grid">
        <div>
            <span class="section-kicker">¿QUIÉNES SOMOS?</span>
            <h2>Una academia nacida en nuestra comunidad.</h2>
        </div>
        <div class="body-copy">
            <p>La Academia Integral de Fútbol Profesor Ramón Sánchez es un proyecto pedagógico orientado a la formación integral de niños y jóvenes de la comunidad Sitio del Niño, en sus dimensiones personal, social y deportiva.</p>
            <p>La iniciativa nació con el propósito de contar con una academia de fútbol con sede en la propia comunidad, donde el deporte también sea un medio para fortalecer el compañerismo, el respeto por los demás y el juego limpio.</p>
        </div>
    </div>
</section>

<section id="mision-vision" class="section section-purpose">
    <div class="container">
        <div class="section-head">
            <div><span class="section-kicker">NUESTRO COMPROMISO</span><h2>Misión y visión</h2></div>
            <p>El propósito institucional definido por los fundadores.</p>
        </div>
        <div class="purpose-grid">
            <article class="purpose-card">
                <span class="purpose-label">01 / MISIÓN</span>
                <h3>Formación integral a través del fútbol</h3>
                <p>Contribuir a la formación integral de los niños y jóvenes de nuestro país, utilizando la práctica del fútbol como un valioso medio para su desarrollo formativo.</p>
            </article>
            <article class="purpose-card">
                <span class="purpose-label">02 / VISIÓN</span>
                <h3>Compromiso social y talento deportivo</h3>
                <p>Constituirnos como una institución deportiva reconocida por su compromiso social con la formación integral del talento de nuestros niños y jóvenes.</p>
            </article>
        </div>
    </div>
</section>

<section id="categorias" class="section section-light">
    <div class="container">
        <div class="section-head">
            <div><span class="section-kicker">FORMACIÓN POR ETAPAS</span><h2>Nuestras categorías</h2></div>
            <p>Un acompañamiento deportivo de acuerdo con la etapa de desarrollo.</p>
        </div>
        <div class="categories-grid">
            <?php foreach (['U7', 'U9', 'U11', 'U13', 'U17'] as $category): ?>
                <article class="category-card">
                    <span class="category-symbol" aria-hidden="true">↗</span>
                    <h3><?= esc($category) ?></h3>
                    <p>Aprendizaje, juego y desarrollo deportivo.</p>
                </article>
            <?php endforeach ?>
        </div>
    </div>
</section>

<section id="beneficios" class="section section-navy">
    <div class="container">
        <span class="section-kicker">NUESTROS OBJETIVOS</span>
        <h2>Más que aprender a jugar fútbol.</h2>
        <p class="section-navy-intro">Buscamos brindar formación básica futbolística y humana con el acompañamiento de entrenadores y especialistas responsables.</p>
        <div class="values-grid">
            <article><span class="value-icon" aria-hidden="true">01</span><h3>Integración y valores</h3><p>Fomentar el fútbol como espacio de convivencia, formación social y compañerismo.</p></article>
            <article><span class="value-icon" aria-hidden="true">02</span><h3>Creatividad y habilidades</h3><p>Estimular el juego, la creatividad, la motricidad y el desarrollo físico y técnico.</p></article>
            <article><span class="value-icon" aria-hidden="true">03</span><h3>Vida saludable</h3><p>Promover el aprovechamiento del tiempo libre, la actividad física y hábitos saludables.</p></article>
        </div>
    </div>
</section>

<?php if ($publicPhotos !== []): ?>
<section id="galeria" class="section editorial-gallery-section" aria-labelledby="gallery-heading">
    <div class="container">
        <div class="editorial-gallery-top">
            <div><span class="section-kicker">NUESTRA ACADEMIA EN ACCIÓN</span><h2 id="gallery-heading">Historias que se viven <em>en la cancha.</em></h2></div>
            <p>Momentos de aprendizaje, trabajo en equipo y experiencias que fortalecen nuestra comunidad deportiva.</p>
        </div>
        <div class="editorial-gallery">
            <?php foreach ($publicPhotos as $index => $photo): ?>
                <figure class="editorial-gallery-item <?= $index === 0 ? 'editorial-gallery-featured' : '' ?>">
                    <img src="<?= esc($photo['src'], 'attr') ?>" alt="<?= esc($photo['alt'], 'attr') ?>" loading="lazy" decoding="async" width="1100" height="760">
                    <figcaption><span><?= esc($photo['caption']) ?></span><small>ACADEMIA RAMÓN SÁNCHEZ</small></figcaption>
                </figure>
            <?php endforeach ?>
        </div>
        <p class="editorial-photo-note">Fotografías compartidas con autorización de publicación y reconocimiento de los derechos correspondientes.</p>
    </div>
</section>
<?php endif ?>

<section id="fundamentacion" class="section">
    <div class="container section-grid">
        <div>
            <span class="section-kicker">NUESTRA FUNDAMENTACIÓN</span>
            <h2>El fútbol como herramienta para crecer.</h2>
        </div>
        <div class="body-copy">
            <p>Creemos que el deporte ofrece a niños y jóvenes una oportunidad de aprovechar positivamente su tiempo libre y fortalecer su bienestar físico, mental y social.</p>
            <p>Por eso, impulsamos el primer acercamiento al fútbol mediante experiencias lúdicas y formativas, en un ambiente que promueva la amistad, la unidad, la solidaridad, el respeto, la superación y la disciplina.</p>
            <p class="muted">Nuestra labor deportiva busca desarrollar estrategias de enseñanza apropiadas para cada etapa, mejorar las habilidades motrices y facilitar el aprendizaje del juego.</p>
        </div>
    </div>
</section>

<section id="contacto" class="section section-light">
    <div class="container contact-panel">
        <div>
            <span class="section-kicker">CONOCE NUESTRA ACADEMIA</span>
            <h2>Ven a vivir tu primera experiencia.</h2>
            <p>La primera clase de prueba es gratuita. Dennis Sánchez recibe a las familias interesadas y explica la dinámica de los entrenamientos y el proceso de ingreso.</p>
        </div>
        <div class="contact-details">
            <strong>Entrenamientos habituales</strong>
            <p>Miércoles y jueves · 4:30 a 6:00 p. m.</p>
            <strong class="contact-label">Ubicación institucional</strong>
            <p>Colonia Sitio del Niño, 1.ª calle y avenida Central, n.º 20-B, San Juan Opico, La Libertad, El Salvador.</p>
            <div class="inquiry-panel">
                <h3>Solicita información</h3>
                <p class="muted">Un integrante autorizado de la academia revisará tu solicitud. Completa solo los datos del adulto responsable.</p>
                <?php if (session('inquiry_success')): ?>
                    <p class="inquiry-feedback inquiry-success" role="status">Recibimos tu consulta. La academia podrá contactarte mediante los datos proporcionados.</p>
                <?php endif ?>
                <?php if (session('inquiry_error')): ?>
                    <p class="inquiry-feedback inquiry-error" role="alert"><?= esc(session('inquiry_error')) ?></p>
                <?php endif ?>
                <form class="inquiry-form" method="post" action="<?= esc(site_url('solicitar-informacion'), 'attr') ?>">
                    <?= csrf_field() ?>
                    <div class="inquiry-honeypot" aria-hidden="true">
                        <label for="inquiry-website">No completar este campo</label>
                        <input id="inquiry-website" name="website" type="text" tabindex="-1" autocomplete="off">
                    </div>
                    <label for="inquiry-name">Nombre del adulto responsable</label>
                    <input id="inquiry-name" name="contact_name" type="text" minlength="3" maxlength="120" autocomplete="name" required>
                    <label for="inquiry-email">Correo electrónico</label>
                    <input id="inquiry-email" name="email" type="email" maxlength="254" autocomplete="email" required>
                    <label for="inquiry-phone">Teléfono (opcional)</label>
                    <input id="inquiry-phone" name="phone" type="tel" maxlength="30" autocomplete="tel">
                    <label for="inquiry-category">Categoría de interés</label>
                    <select id="inquiry-category" name="category" required>
                        <option value="not_sure">Por definir</option>
                        <?php foreach (['U7', 'U9', 'U11', 'U13', 'U17'] as $category): ?>
                            <option value="<?= esc($category, 'attr') ?>"><?= esc($category) ?></option>
                        <?php endforeach ?>
                    </select>
                    <label for="inquiry-message">Mensaje (opcional)</label>
                    <textarea id="inquiry-message" name="message" maxlength="500" rows="3" placeholder="Cuéntanos qué información necesitas. No incluyas nombres ni datos médicos de menores."></textarea>
                    <label class="inquiry-consent"><input type="checkbox" name="privacy_consent" value="1" required><span>Confirmo que soy mayor de edad y autorizo que la academia utilice estos datos únicamente para responder a mi solicitud. <a href="<?= esc(site_url('privacidad'), 'attr') ?>" target="_blank" rel="noopener">Leer aviso de privacidad</a>.</span></label>
                    <button type="submit">Enviar consulta</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
