<?php

namespace App\Controllers;

use Config\PublicGallery;
use App\Services\SiteLaunchMode;

class Home extends BaseController
{
    public function index(): string
    {
        if (SiteLaunchMode::isPrelaunch()) {
            service('response')->setHeader('Cache-Control', 'public, max-age=300')
                ->setHeader('X-Robots-Tag', 'index, follow');
            return view('public/prelaunch');
        }

        $config = config(PublicGallery::class);
        $photos = [];

        if ($config->approvedForPublication) {
            foreach ($config->photos as $photo) {
                if (($photo['approved'] ?? false) !== true) {
                    continue;
                }
                $file = (string) ($photo['file'] ?? '');
                // The filenames come only from the server-side curated catalog.
                if ($file === '' || basename($file) !== $file || ! preg_match('/^[a-z0-9-]+\\.webp$/', $file)) {
                    continue;
                }

                if (is_file(FCPATH . 'assets/img/gallery/' . $file)) {
                    $photo['src'] = base_url('assets/img/gallery/' . $file);
                    $photos[] = $photo;
                }
            }
        }

        return view('public/home', ['publicPhotos' => $photos]);
    }
}
