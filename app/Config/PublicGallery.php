<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Public publication is off by default for every image of identifiable minors.
 * Set `approvedForPublication` to true ONLY after verifying family
 * consent for public website use AND the photographer's usage rights.
 * Do not store personal information or consent evidence here.
 */
final class PublicGallery extends BaseConfig
{
    public bool $approvedForPublication = false;

    /** @var array<int,array{file:string,alt:string,caption:string,feature:string}> */
    public array $photos = [
        [
            'file' => 'equipo-formacion.webp',
            'alt' => 'Equipo infantil de la academia junto a su entrenador en la cancha',
            'caption' => 'Un equipo, una comunidad',
            'feature' => 'team',
        ],
        [
            'file' => 'juego-equipo.webp',
            'alt' => 'Niños de la academia participando en una jugada de fútbol',
            'caption' => 'Aprender jugando',
            'feature' => 'action',
        ],
        [
            'file' => 'accion-partido.webp',
            'alt' => 'Jóvenes futbolistas en una acción durante un partido',
            'caption' => 'El esfuerzo en cada jugada',
            'feature' => 'action',
        ],
        [
            'file' => 'entrenamiento-tecnico.webp',
            'alt' => 'Futbolistas de la academia realizando ejercicios técnicos en la cancha',
            'caption' => 'Formación constante',
            'feature' => 'training',
        ],
        [
            'file' => 'portero-formacion.webp',
            'alt' => 'Portero juvenil concentrado frente al arco',
            'caption' => 'Cada posición importa',
            'feature' => 'training',
        ],
        [
            'file' => 'valores-equipo.webp',
            'alt' => 'Jugadores de la academia compartiendo una jornada deportiva',
            'caption' => 'Crecer juntos',
            'feature' => 'community',
        ],
    ];
}
