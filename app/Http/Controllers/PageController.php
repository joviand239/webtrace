<?php

namespace App\Http\Controllers;

use App\Service\TracService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $credential = (object)[
            'username' => 'pwpgps01@gmail.com',
            'password' => '123456'
        ];

        $units = TracService::GetUnits($credential);

        $user = Auth::user();
        $user->password = bcrypt('cjlogistics');
        $user->save();


        return view('home', [
            'units' => @$units,
        ]);
    }
}
