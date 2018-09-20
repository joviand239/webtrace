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

        $geolocationApi = 'https://geocode-maps.wialon.com/hst-api.wialon.com/gis_geocode';

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
            $uid = $authData->user->id;
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
                $lng = null;
                $lat = null;
                $time = null;
                $speed = null;
                $rawTime = null;
                $address = [null];

                if ($unit->pos) {
                    $rawTime = @$unit->pos->t;
                    $time = date("YmdHis", substr($rawTime, 0, 10));

                    $lng = @$unit->pos->x;
                    $lat = @$unit->pos->y;
                    $speed = @$unit->pos->s;



                    $responseAddress = $client->request('POST', $geolocationApi, [
                        'form_params' => [
                            'coords' => '[{"lon":'.$lng.',"lat":'.$lat.'}]',
                            'flags' => 1255211008,
                            'uid' => $uid,
                        ]
                    ]);

                    $address = json_decode($responseAddress->getBody()->getContents());


                }

                $tempunit = [
                    'plate' => @$unit->nm,
                    'datetime' => $time,
                    'lng' => $lng,
                    'lat' => $lat,
                    'engine' => true,
                    'speed' => $speed,
                    'angle' => null,
                    'location' => @$address[0]
                ];

                $responseData[] = (object)$tempunit;
            }
        }

        return ResponseUtil::Success(['units' => @$responseData]);
    }
}
