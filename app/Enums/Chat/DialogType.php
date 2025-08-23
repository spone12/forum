<?php

namespace App\Enums\Chat;

enum DialogType:string
{
    case PRIVATE = 'private';
    case GROUP  = 'group';
}
