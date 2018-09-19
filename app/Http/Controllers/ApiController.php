<?php

namespace App\Http\Controllers;

use App\Util\ResponseUtil;
use Illuminate\Http\Request;

class ApiController extends Controller
{

    public function getData($token)
    {
        $wialonApi = 'https://hst-api.wialon.com/wialon/ajax.html';

        $restrictUnit = [
            18015044,
        ];

        if (!$token) {
            return ResponseUtil::Unauthorized('Token Unauthorized, please check again');
        }

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

            if (in_array($unit->id, $restrictUnit)) {
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
