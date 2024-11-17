<?php
namespace App\Traits;

use App\Enums\Profile\ProfileEnum;
use Illuminate\Support\Collection;

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
     * @param mixed $data
     * @return void
     */
    public static function noAvatar(&$data): void {

        if (empty($data)) {
            return;
        }

        if (!is_array($data) && !$data instanceof Collection) {
            if (is_null($data->avatar) || stripos($data->avatar, 'no_avatar') !== false) {
                $data->avatar = asset(ProfileEnum::NO_AVATAR);
            } else {
                $data->avatar = asset('storage/' . $data->avatar);
            }
        } else {
            foreach ($data as $k => $v) {
                if (is_null($v->avatar) || stripos($v->avatar, 'no_avatar') !== false) {
                    $data[$k]->avatar = asset(ProfileEnum::NO_AVATAR);
                } else {
                    $data[$k]->avatar = asset('storage/' . $v->avatar);
                }
            }
        }
    }
}
