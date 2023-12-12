<?php

namespace App\EventListener;

use App\Entity\Media;
use App\Enum\MediaCache;
use App\EventListener\Features\RemoveMediaTrait;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Liip\ImagineBundle\Message\WarmupCache;

class ImageCacheCleaner
{
    use RemoveMediaTrait;

    private ?string $oldImageCacheName = null;
    private ?Media $media;

    public function postPersist(Media $media): void
    {
        $this->messageBus->dispatch(new WarmupCache(MediaCache::UploadMediaFolder->value . $media->getImageName()));
    }

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
            $this->removeMediaPath($this->media);
            $this->removeMediaCache($this->oldImageCacheName);
        }

        $this->messageBus->dispatch(new WarmupCache(MediaCache::UploadMediaFolder->value . $media->getImageName()));
    }
}