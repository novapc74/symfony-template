<?php

namespace App\Enum;

enum PageBlockType: string
{
    case REQUIRED = 'required';
    case QUOTE = 'quote';
    case TITLE_DESCRIPTION_IMAGE = 'title_description_image';
    case DESCRIPTION_IMAGE = 'description_image';
    case TITLE_HTML_GALLERY = 'title_html_gallery';
}