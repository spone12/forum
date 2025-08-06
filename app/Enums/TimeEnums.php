<?php

namespace App\Enums;

/**
 * Enum TimeEnums
 *
 * @package App\Enums
 */
enum TimeEnums:int
{
    case SECOND = 1;
    case MINUTE = 60;
    case HOUR = 3600;
    case DAY = 86400;
    case WEEK = 604800;
    case MONTH = 2592000;
    case YEAR = 31536000;
}
