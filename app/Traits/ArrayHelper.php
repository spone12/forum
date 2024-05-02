<?php

namespace App\Traits;

use App\Enums\Profile\ProfileEnum;

/**
 * Trait ArrayHelper
 *
 * @package App\Traits
 */
trait ArrayHelper
{
    /**
     * Changing the path of empty avatars
     *
     * @param midex $array
     * @return void
     */
    public static function noAvatar(&$array): void {
        
        if (empty($array)) {
            return;
        }

        foreach ($array as $k => $v) {
            if (is_null($v->avatar)) {
                $array[$k]->avatar = ProfileEnum::NO_AVATAR;
            }
        }
    }
}
