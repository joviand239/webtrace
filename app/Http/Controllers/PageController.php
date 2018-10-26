<?php

namespace App\Http\Controllers;

use App\Service\WialonService;
use Illuminate\Http\Request;

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
        //$token = '0956dd1275414f18f1f5f719d671b3c765DA023CE5CBED70D136ADFD27E0D1CBE8C1D208';

        $token = '3aac7ec203e360bc45ee55a6bd8c449303B2109BB4B7CC1EDC632A51AD1E758FE15D6D3E';

        $units = WialonService::GetUnits($token, []);


        return view('home', [
            'units' => @$units,
        ]);
    }
}
