<?php

namespace App\Http\Controllers;

use App\Enums\ResponseCodeEnum;
use App\Service\{HomeService, NotificationsService};

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
     * @OA\Get(
     *     path="/home",
     *     summary="Home page",
     *     tags={"Home"},
     *     @OA\Response(response=200, description="Successful response")
     * )
     *
     * Show the home page
     *
     * @return \Illuminate\Contracts\Foundation\Application|
     * \Illuminate\Contracts\View\Factory|
     * \Illuminate\Contracts\View\View|\never
     */
    public function index()
    {
        try {
            $notations = $this->homeService->notations('');
            NotificationsService::userNotifications();
        } catch (\Throwable $e) {
            return abort(ResponseCodeEnum::NOT_FOUND);
        }

        return view('home', compact('notations'));
    }
}
