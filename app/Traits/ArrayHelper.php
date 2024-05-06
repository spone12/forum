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
     * @param midex $data
     * @return void
     */
    public static function noAvatar(&$data): void {

        if (empty($data)) {
            return;
        }

        if (!is_array($data)) {
            if (is_null($data->avatar)) {
                $data->avatar = ProfileEnum::NO_AVATAR;
            }
        } else {
            foreach ($data as $k => $v) {
                if (is_null($v->avatar)) {
                    $data[$k]->avatar = ProfileEnum::NO_AVATAR;
                }
            }
        }
    }
}
