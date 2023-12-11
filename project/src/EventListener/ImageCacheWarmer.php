<?php

namespace App\EventListener;

use App\Entity\Media;
use App\EventListener\Features\RemoveMediaTrait;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class ImageCacheWarmer
{
    use RemoveMediaTrait;

    private ?string $oldImageCacheName = null;
    private ?Media $media;

    public function preUpdate(Media $media): void
    {
        $this->oldImageCacheName = $media->getImageName();
        $this->media = $media;
    }

    public function postUpdate(Media $media, LifecycleEventArgs $args): void
    {
        $unitOfWork = $args->getObjectManager()->getUnitOfWork();
        $imageChanges = $unitOfWork->getEntityChangeSet($media);

        if (array_key_exists('imageName', $imageChanges) && $this->oldImageCacheName) {
            $this->removeImageCache($this->oldImageCacheName);
            $this->removeMediaPath($this->media);
        }
    }
}