<?php
namespace App\Service;

use App\Util\ResponseUtil;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

class TracService {


    public static function GetAccessToken($credential) {
        $baseUrl = 'http://gps.tracmanager.com/api/open/v1';

        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->request('POST', $baseUrl.'/login', [
                'body' => json_encode($credential)
            ]);
        } catch (RequestException $e) {
            return false;
        }

        $responseData = json_decode($response->getBody()->getContents());

        return @$responseData->data->access_token;
    }


    public static function GetUnits($credential) {
        $baseUrl = 'http://gps.tracmanager.com/api/open/v1';

        $access_token = self::GetAccessToken($credential);

        if (!$access_token) {
            return ResponseUtil::Error('Access Token Not Found');
        }

        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->request('GET', $baseUrl.'/trackers', [
                'headers' => [
                    'access_token' => $access_token
                ]
            ]);
        } catch (RequestException $e) {
            return ResponseUtil::Error('404 Bad Request');
        }

        $responseData = json_decode($response->getBody()->getContents());

        $tempUnits = [];

        if (@$responseData->status == 200) {
            $tempUnits = $responseData->data;
        }else {
            return ResponseUtil::Error('404 Not Found');
        }

        $units = [];

        foreach ($tempUnits as $unit) {

            $location = self::GetUnitLocation($access_token, @$unit->sn);

            $tempData = (object)[
                'id' => @$unit->sn,
                'name' => @$unit->alias,
                'address' => '',
                'longitude' => 0,
                'latitude' => 0
            ];

            if ($location) {
                $tempData->address = @$location->name;
                $tempData->longitude = @$location->longitude;
                $tempData->latitude = @$location->latitude;
            }

            $units[] = $tempData;
        }

        return $units;
    }


    public static function GetUnitLocation($access_token, $trackerId) {
        $baseUrl = 'http://gps.tracmanager.com/api/open/v1';

        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->request('GET', $baseUrl.'/trackers/'.$trackerId.'/location', [
                'headers' => [
                    'access_token' => $access_token
                ]
            ]);
        } catch (RequestException $e) {
            return false;
        }

        $responseData = json_decode($response->getBody()->getContents());

        if (@$responseData->status == 200) {
            return @$responseData->data;
        }else {
            return false;
        }
    }
}