<?php

namespace App\Http\Controllers;

use App\Enums\{ResponseCodeEnum, TimeEnums};
use App\Service\{HomeService, NotificationsService};
use Illuminate\Support\Facades\Cache;

/**
 * Class HomeController
 *
 * @package App\Http\Controllers
 */
class HomeController extends Controller
{
    /**
     * @var HomeService
     */
    protected $homeService;

    /**
     * HomeController constructor.
     *
     * @param HomeService $homeService
     */
    function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;
    }

    /**
     * Show the home page
     *
     * @return \Illuminate\Contracts\Foundation\Application|
     * \Illuminate\Contracts\View\Factory|
     * \Illuminate\Contracts\View\View|\never
     */
    public function index()
    {
        try {
            $page = request()->get('page', 1);
            $cacheHomeKey = "home_page_{$page}";

            $notations = Cache::remember(
                $cacheHomeKey,
                now()->addSeconds(TimeEnums::MINUTE->value),
                function () {
                    return $this->homeService->notations('');
            });

            NotificationsService::userNotifications();
        } catch (\Throwable $e) {
            return abort(ResponseCodeEnum::NOT_FOUND);
        }

        return view('home', compact('notations'));
    }
}
