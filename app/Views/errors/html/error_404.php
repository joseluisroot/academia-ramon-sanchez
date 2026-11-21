<?php
/**
 * Vista de respaldo para 404 cuando no se puede utilizar el override de rutas.
 * No revelar la ruta solicitada ni el mensaje de excepción.
 */
echo view('errors/institutional', [
    'statusCode' => 404,
    'eyebrow' => 'NOS SALIMOS DE LA CANCHA',
    'title' => 'No encontramos esa jugada.',
    'message' => 'La página que buscas no existe o el enlace ya no está disponible. Regresa al inicio para continuar tu recorrido con la academia.',
    'illustration' => 'not-found.svg',
    'illustrationAlt' => 'Recorrido de fútbol con una lupa que muestra el número 404.',
]);
