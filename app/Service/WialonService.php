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

        $zones = self::GetZones($token);

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
                $lng = null;
                $lat = null;
                $time = null;
                $speed = null;
                $rawTime = null;
                $address = [null];
                $geofence = null;
                $status = null;

                if ($unit->pos) {
                    $rawTime = @$unit->pos->t;
                    $time = date("Y-m-d H:i:s", substr($rawTime, 0, 10));

                    $lng = @$unit->pos->x;
                    $lat = @$unit->pos->y;
                    $speed = @$unit->pos->s;

                    $geofence = self::GetGeofence($lng, $lat, @$zones);

                    if ($geofence) {
                        if ($geofence == 'Indorama Polypet'){
                            $status = 'PTIP';
                        }else if ($geofence == 'IRS' || $geofence == 'IVI' || $geofence == 'IPCI'){
                            $status = 'CUST';
                        }else if ($geofence == 'PTIP'){
                            $status = 'OTW';
                        }
                    }



                    //$address = self::GetAddressUnit($userId, $lng, $lat);
                }

                $tempunit = [
                    'plate' => @$unit->nm,
                    'datetime' => $time,
                    'geofence' => @$geofence,
                    'status' => @$status,
                ];

                $listUnit[] = (object)$tempunit;
            }
        }

        return $listUnit;
    }


    public static function GetZones($token) {

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
                        "itemsType": "avl_resource",
                        "propName": "zones_library",
                        "propType":"propitemname", 
                        "propValueMask": "!",
                        "sortType": "sys_name"
                    },
                    "force": 1,
                    "flags": "4097",
                    "from": 0,
                    "to": 0
                }',
                'sid' => $sid,
            ]
        ]);

        $responseData = json_decode($response->getBody()->getContents());

        if (isset($responseData->items[0]->zl)) {
            $responseData = $responseData->items[0]->zl;
        }else {
            return false;
        }

        $zones = [];

        foreach ($responseData as $key => $unit) {
            $tempData = [
                'name' => @$unit->n,
                'location' => @$unit->d,
                'min_lng' => @$unit->b->min_x,
                'min_lat' => @$unit->b->min_y,
                'max_lng' => @$unit->b->max_x,
                'max_lat' => @$unit->b->max_y,
            ];

            $zones[] = (object)$tempData;
        }

        return $zones;
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

    public static function GetGeofence($longitude, $latitude, $zones = []) {
        $returnGeofence = null;

        foreach ($zones as $zone) {

            if (($longitude >= $zone->min_lng && $longitude <= $zone->max_lng) && ($latitude >= $zone->min_lat && $latitude <= $zone->max_lat)) {
                $returnGeofence = @$zone->name;
            }

        }

        return @$returnGeofence;
    }

}