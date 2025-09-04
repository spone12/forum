<?php

namespace App\Enums\Chat;

enum ChatRole:string
{
    case MEMBER = 'member';
    case ADMIN  = 'admin';
    case OWNER  = 'owner';
}
