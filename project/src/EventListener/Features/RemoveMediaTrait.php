<?php

namespace App\EventListener\Features;

use App\Entity\Media;
use App\Enum\MediaCache;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

trait RemoveMediaTrait
{
    public function __construct(private readonly EntityManagerInterface $entityManager,
                                private readonly CacheManager           $imagineCacheManager)
    {
    }

    private function removeMediaPath(?Media $media): void
    {
        if ($media) {
            $fileSystem = new Filesystem();
            $imagePath = '%kernel.project_dir%/public' . MediaCache::UploadMediaFolder->value . $media->getImageName();

            $fileSystem->remove($imagePath);
        }
    }

    private function removeMedia(?Media $media): void
    {
        $this->entityManager->remove($media);
    }

    private function removeImageCache(string $imageName): void
    {
        $imagePath = MediaCache::UploadMediaFolder->value . $imageName;

        $this->imagineCacheManager->remove($imagePath);
        $this->imagineCacheManager->remove("$imagePath.webp");
    }
}