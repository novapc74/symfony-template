<?php

namespace App\Entity\Trait;

use App\Entity\Media;
use App\Enum\MediaCache;

trait CacheMediaPathTrait
{
    public function getMediaCachePath(?Media $media, ?string $filter = 'middle_banner'): ?string
    {
        if (null === $media) {
            return null;
        }

        $imageName = $media->getImageName();

        return in_array($media->getMimeType(), Media::getFilterableExtensions())
            ? MediaCache::MediaCacheFolder->value . $filter . MediaCache::UploadMediaFolder->value . $imageName
            : MediaCache::UploadMediaFolder->value . $imageName;
    }
}