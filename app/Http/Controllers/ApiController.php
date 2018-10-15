<?php

namespace App\Http\Controllers;

use App\Service\WialonService;
use App\Util\ResponseUtil;
use Illuminate\Http\Request;

class ApiController extends Controller
{

    public function getData($key)
    {
        $apiKey = 'b3nM1GMzGI';

        $token = '0956dd1275414f18f1f5f719d671b3c765DA023CE5CBED70D136ADFD27E0D1CBE8C1D208';

        $restrictUnit = [
            'PAN 08', 'PAN 04', 'PAN 05', 'PAN 06', '33 MKT', '37 MKT', '45 MKT', '46 MKT', '32 MKT', '41 MKT', '17 MKT', '18 MKT', '20 MKT', '44 MKT'
        ];

        if (!$key) {
            return ResponseUtil::Unauthorized('Key Unauthorized, please check again');
        }elseif ($key != $apiKey){
            return ResponseUtil::Unauthorized('Key Unauthorized, please check again');
        }


        $list = WialonService::GetUnits($token, $restrictUnit);


        return ResponseUtil::Success(['units' => @$list]);
    }



}
