<?php

namespace App\Http\Controllers;
//namespace App\Http\Model;

use Illuminate\Http\Request;
use App\Http\Model\HomeModel;
//use App\HomeModel;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
       $notations = HomeModel::take_notations();

        return view('home', ['notations' => $notations]);
    }
}
