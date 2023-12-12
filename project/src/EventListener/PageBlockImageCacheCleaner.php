<?php

namespace App\EventListener;

use App\Entity\Gallery;
use App\Entity\PageBlock;
use App\Enum\MediaCache;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Liip\ImagineBundle\Message\WarmupCache;
use App\EventListener\Features\RemoveMediaTrait;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\EventListener\Features\SetForeignKeyCheckAsNullTrait;

class PageBlockImageCacheCleaner
{
    use RemoveMediaTrait, SetForeignKeyCheckAsNullTrait;

    public function postUpdate(PageBlock $pageBlock, LifecycleEventArgs $args): void
    {
        $this->setForeignKeyChecksAsNull($args);

        $unitOfWork = $args->getObjectManager()->getUnitOfWork();
        $pageBlockChanges = $unitOfWork->getEntityChangeSet($pageBlock);

        if (array_key_exists('image', $pageBlockChanges) && $oldMedia = $pageBlockChanges['image'][0]) {
            $this->removeMedia($oldMedia);

            $this->messageBus->dispatch(new WarmupCache(MediaCache::UploadMediaFolder->value . $pageBlock->getImage()->getImageName(), ['medium']));
        }
    }

    public function preRemove(PageBlock $pageBlock, PreRemoveEventArgs $args): void
    {
        $this->setForeignKeyChecksAsNull($args);

        if ($image = $pageBlock->getImage()) {
            $this->removeMedia($image);
        }

        $pageBlock
            ->getGallery()
            ->map(fn(Gallery $gallery) => $this->removeMedia($gallery->getImage()));
    }
}