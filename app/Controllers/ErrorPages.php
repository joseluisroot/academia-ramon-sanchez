<?php

declare(strict_types=1);

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

final class ErrorPages extends BaseController
{
    /**
     * Código 403 real incluso si Shield redirige a esta URL
     * por falta de permiso. No exponer información sensible.
     */
    public function forbidden(): ResponseInterface
    {
        return $this->response
            ->setStatusCode(403)
            ->setHeader('Cache-Control', 'no-store, private')
            ->setBody(view('errors/institutional', [
                'statusCode' => 403,
                'eyebrow' => 'FUERA DE TU ZONA DE ACCESO',
                'title' => 'Este espacio está reservado.',
                'message' => 'En nuestra academia cada integrante tiene un rol en el equipo. Tu cuenta no cuenta con autorización para acceder a esta sección. Si necesitas ingresar, consulta con la administración.',
                'illustration' => 'forbidden-access.svg',
                'illustrationAlt' => 'Cancha de fútbol y portería protegida por un candado.',
            ]));
    }

    /**
     * Handler del override 404 de CI4: se invoca directamente
     * y no debe recibir ni mostrar el mensaje de excepción.
     */
    public function notFound(?string $ignored = null): ResponseInterface
    {
        return $this->response
            ->setStatusCode(404)
            ->setHeader('Cache-Control', 'no-store')
            ->setBody(view('errors/institutional', [
                'statusCode' => 404,
                'eyebrow' => 'NOS SALIMOS DE LA CANCHA',
                'title' => 'No encontramos esa jugada.',
                'message' => 'La página que buscas no existe o el enlace ya no está disponible. Regresa al inicio para continuar tu recorrido con la academia.',
                'illustration' => 'not-found.svg',
                'illustrationAlt' => 'Recorrido de fútbol con una lupa que muestra el número 404.',
            ]));
    }
}
