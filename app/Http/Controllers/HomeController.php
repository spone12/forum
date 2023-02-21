<?php

namespace App\Http\Controllers;
use App\Models\HomeModel;

class HomeController extends Controller
{

    /**
     * Show the home page
     *
     * @return
     */
    public function index()
    {
       $notations = HomeModel::take_notations();

        return view('home', compact('notations'));
    }
}
