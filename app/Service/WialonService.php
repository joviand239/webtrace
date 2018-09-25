<?php
namespace App\Service;

use App\Util\ResponseUtil;

class WialonService {


    public static function GetUserData($token) {
        $wialonApi = 'https://hst-api.wialon.com/wialon/ajax.html';

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $wialonApi, [
            'form_params' => [
                'svc' => 'token/login',
                'params' => '{"token":"'.$token.'"}'
            ]
        ]);

        $responseData = json_decode($response->getBody()->getContents());


        return @$responseData;
    }


    public static function GetUnits($token, $restrictUnit = []) {

        $wialonApi = 'https://hst-api.wialon.com/wialon/ajax.html';

        $authData = self::GetUserData($token);

        $sid = null;
        $userId = null;

        if (isset($authData->eid)){
            $sid = $authData->eid;
            $userId = $authData->user->id;
        }else {
            return false;
        }

        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST', $wialonApi, [
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

        $responseData = json_decode($response->getBody()->getContents());



        if (isset($responseData->items)) {
            $responseData = $responseData->items;
        }else {
            return false;
        }

        $listUnit = [];

        foreach ($responseData as $key => $unit) {

            if (in_array($unit->nm, $restrictUnit) || !count($restrictUnit)) {
                $lng = 0;
                $lat = 0;
                $time = '';
                $speed = '';
                $rawTime = '';
                $address = [''];

                if ($unit->pos) {
                    $rawTime = @$unit->pos->t;
                    $time = date("YmdHis", substr($rawTime, 0, 10));

                    $lng = @$unit->pos->x;
                    $lat = @$unit->pos->y;
                    $speed = @$unit->pos->s;

                    $address = self::GetAddressUnit($userId, $lng, $lat);
                }

                $tempunit = [
                    'plate' => @$unit->nm,
                    'datetime' => $time,
                    'lng' => $lng,
                    'lat' => $lat,
                    'engine' => true,
                    'speed' => $speed,
                    'angle' => null,
                    'location' => @$address,
                ];

                $listUnit[] = (object)$tempunit;
            }
        }

        return $listUnit;
    }


    public static function GetAddressUnit($userId, $longitude, $latitude) {
        $client = new \GuzzleHttp\Client();

        $geolocationApi = 'https://geocode-maps.wialon.com/hst-api.wialon.com/gis_geocode';

        $response = $client->request('POST', $geolocationApi, [
            'form_params' => [
                'coords' => '[{"lon":'.$longitude.',"lat":'.$latitude.'}]',
                'flags' => 1255211008,
                'uid' => $userId,
            ]
        ]);

        $responseData = json_decode($response->getBody()->getContents());


        return @$responseData[0];
    }

}
