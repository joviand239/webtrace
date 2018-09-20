<?php

namespace App\Http\Controllers;

use App\Util\ResponseUtil;
use Illuminate\Http\Request;

class ApiController extends Controller
{

    public function getData($key)
    {
        $apiKey = 'b3nM1GMzGI';

        $wialonApi = 'https://hst-api.wialon.com/wialon/ajax.html';

        $restrictUnit = [
            '61 MKT', '62 MKT', '63 MKT', '64 MKT', '65 MKT', '67 MKT', '69 MKT', '70 MKT', '71 MKT', '72 MKT', '81 MKT', '82 MKT', '83 MKT', '84 MKT', '85 MKT'
        ];

        if (!$key) {
            return ResponseUtil::Unauthorized('Key Unauthorized, please check again');
        }elseif ($key != $apiKey){
            return ResponseUtil::Unauthorized('Key Unauthorized, please check again');
        }

        $token = 'aa8973ca6e3ceb0a9cfa6df027dc11ffF5C47C5913FB976C41861B9BBF96623CC84ADFC2';

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $wialonApi, [
            'form_params' => [
                'svc' => 'token/login',
                'params' => '{"token":"'.$token.'"}'
            ]
        ]);

        $authData = json_decode($response->getBody()->getContents());

        $sid = null;

        if (isset($authData->eid)){
            $sid = $authData->eid;
        }else {
            return ResponseUtil::Unauthorized('Token Unauthorized, please check again');
        }

        $responseUnits = $client->request('POST', $wialonApi, [
            'form_params' => [
                'svc' => 'core/search_items',
                'params' => '{"spec": {
                        "itemsType": "avl_unit",
                        "propName": "sys_name",
                        "propValueMask": "*",
                        "sortType": "sys_name"
                    },
                    "force": 1,
                    "flags": "1025",
                    "from": 0,
                    "to": 0
                }',
                'sid' => $sid,
            ]
        ]);

        $rawUnits = json_decode($responseUnits->getBody()->getContents());

        $listUnit = [];

        if (isset($rawUnits->items)) {
            $listUnit = $rawUnits->items;
        }else {
            return ResponseUtil::Unauthorized('Token Unauthorized, please check again');
        }

        $responseData = [];

        foreach ($listUnit as $key => $unit) {

            if (in_array($unit->nm, $restrictUnit)) {
                $tempunit = [
                    'name' => @$unit->nm,
                    'long' => @$unit->pos->x,
                    'lang' => @$unit->pos->y,
                    'speed' => @$unit->pos->s,
                ];

                $responseData[] = (object)$tempunit;
            }
        }

        return ResponseUtil::Success(['units' => @$responseData]);
    }
}
