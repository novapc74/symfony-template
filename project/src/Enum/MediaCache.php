<?php

namespace App\Enum;

enum MediaCache: string
{
    case MediaCacheFolder = '/media/cache/resolve/';
    case UploadMediaFolder = '/upload/media/';
}