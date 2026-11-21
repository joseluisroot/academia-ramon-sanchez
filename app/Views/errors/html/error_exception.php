<?php
/**
 * Vista mínima para excepciones en desarrollo local.
 * En producción CodeIgniter usa production.php sin información técnica.
 */
?>
<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Error de aplicación · Academia Ramón Sánchez</title>
<style>
body{background:#f5f7fb;color:#071b49;font:16px/1.6 Arial,sans-serif;margin:0;padding:40px}
main{max-width:900px;margin:auto;background:white;padding:32px;border-radius:18px;border:1px solid #dce6f0}
pre{overflow:auto;background:#edf2f8;border-radius:10px;padding:16px;font-size:12px;color:#24324a}
</style></head>
<body><main><h1>Error de aplicación</h1>
<p>Durante el desarrollo se ha detectado una excepción. Corrige el problema antes de publicar la plataforma.</p>
<p><strong><?= esc(get_class($exception)) ?>:</strong> <?= esc($exception->getMessage()) ?></p>
<pre><?= esc($exception->getFile() . ':' . $exception->getLine()) ?></pre>
</main></body></html>
