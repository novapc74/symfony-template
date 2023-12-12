<?php

namespace App\EventListener;

use App\Entity\Gallery;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use App\EventListener\Features\RemoveMediaTrait;
use App\EventListener\Features\SetForeignKeyCheckAsNullTrait;

class GalleryImageCacheCleaner
{
    use RemoveMediaTrait, SetForeignKeyCheckAsNullTrait;

    public function preRemove(Gallery $gallery, PreRemoveEventArgs $args): void
    {
        $this->setForeignKeyChecksAsNull($args);

        if ($image = $gallery->getImage()) {
            $this->removeMedia($image);
        }
    }
}