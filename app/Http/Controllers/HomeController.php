<?php

namespace App\Http\Controllers;
use App\Service\HomeService;

class HomeController extends Controller
{

    protected $homeService;

    function __construct(HomeService $homeService) {
        $this->homeService = $homeService;
    }

    /**
     * Show the home page
     *
     * @return
     */
    public function index()
    {

        try {
            $notations = $this->homeService->notations();
        } catch (\Throwable $e) {
            return abort(404);
        }

        return view('home', compact('notations'));
    }
}
