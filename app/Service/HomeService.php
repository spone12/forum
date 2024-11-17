<?php

namespace App\Service;

use App\Enums\Profile\ProfileEnum;
use App\Repository\HomeRepository;
use App\Traits\ArrayHelper;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Enums\TimeEnums;

/**
 * Class HomeService
 * @package App\Service
 */
class HomeService
{
    use ArrayHelper;

    /** @var HomeRepository */
    protected $homeRepository;

    /**
     * HomeService constructor.
     * @param HomeRepository $homeRepository
     */
    function __construct(HomeRepository $homeRepository) {
        $this->homeRepository = $homeRepository;
    }

    /**
     * Home service
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function notations(string $order)
    {
        $notations = $this->homeRepository->takeNotations($order);
        if ($notations) {
            foreach ($notations as $k => $v) {
                $notations[$k]->date_n =
                    Carbon::createFromFormat('Y-m-d H:i:s', $notations[$k]->date_n)->diffForHumans();

                ArrayHelper::noAvatar($notations[$k]);

                if (strlen($v->text_notation) >= 250)
                    $notations[$k]->text_notation =  Str::limit($v->text_notation, 250);
            }
        }
        return $notations;
    }

    /**
     * Caches and returns the result of the number messages
     *
     * @return void
     */
    public function userNotifications(): void
    {
        if (Auth::check()) {
            cache()->remember('userNorificationsBell' . Auth::user()->id, TimeEnums::DAY, function () {
                $userNotifications = $this->homeRepository->getUserNotifications();
                ArrayHelper::noAvatar($userNotifications);
                return $userNotifications;
            });
        }
    }
}
