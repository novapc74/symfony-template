<?php

namespace App\EventListener;

use App\Entity\Gallery;
use App\Entity\PageBlock;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use App\EventListener\Features\RemoveMediaTrait;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\EventListener\Features\SetForeignKeyCheckAsNullTrait;

class PageBlockImageCleaner
{
    use RemoveMediaTrait, SetForeignKeyCheckAsNullTrait;

    public function postUpdate(PageBlock $pageBlock, LifecycleEventArgs $args): void
    {
        $this->setForeignKeyChecksAsNull($args);

        $unitOfWork = $args->getObjectManager()->getUnitOfWork();
        $pageBlockChanges = $unitOfWork->getEntityChangeSet($pageBlock);

        if (array_key_exists('image', $pageBlockChanges) && $oldMedia = $pageBlockChanges['image'][0]) {
            $this->removeImageCache($oldMedia->getImageName());
            $this->removeMedia($oldMedia);
        }
    }

    public function preRemove(PageBlock $pageBlock, PreRemoveEventArgs $args): void
    {
        $this->setForeignKeyChecksAsNull($args);

        if ($image = $pageBlock->getImage()) {
            $this->removeImageCache($image->getImageName());
            $this->removeMedia($image);
        }

        $pageBlock->getGallery()->map(function (Gallery $gallery) {
            $image = $gallery->getImage();

            $this->removeImageCache($image->getImageName());
            $this->removeMedia($image);
        });
    }
}