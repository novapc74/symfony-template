<?php

namespace App\Controller;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaController extends AbstractController
{
    private CacheManager $imagineCacheManager;
    public function __construct(CacheManager $imagineCacheManager)
    {
        $this->imagineCacheManager = $imagineCacheManager;
    }

    #[Route('/cache/media/{img_size}/{img_path}', requirements: ["img_size" => "^(?!resolve)\w+","img_path" => ".+"], priority: 40)]
    public function sizedImage(Request $request): Response
    {
        $img_path = $request->attributes->get('img_path');
        $img_size = $request->attributes->get('img_size');

        return $this->redirect($this->imagineCacheManager->getBrowserPath($img_path, $img_size));
    }
}