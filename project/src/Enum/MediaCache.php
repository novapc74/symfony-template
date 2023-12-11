<?php

namespace App\Enum;

enum MediaCache: string
{
    case MediaCacheResolve = '/cache/media/resolve/';
    case MediaCacheFolder = '/cache/media/';
    case UploadMediaFolder = '/upload/media/';
    case CurrentDomain = 'localhost';
    case MediumImageFilter = 'medium';
    case ThumbnailImageFilter = 'thumbnail';
    case Domain = 'http://85.193.91.182';
}