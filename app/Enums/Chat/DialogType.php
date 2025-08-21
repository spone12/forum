<?php

namespace App\Enums\Chat;

enum DialogType:string
{
    case MEMBER = 'private';
    case ADMIN  = 'group';
}
