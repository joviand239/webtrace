<?php

namespace App\Console\Commands;

use App\Service\WialonService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class DataAutomation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hit data to Izzy Track Integrate API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        logger('Automation Start');

        $token = 'aa8973ca6e3ceb0a9cfa6df027dc11ffF5C47C5913FB976C41861B9BBF96623CC84ADFC2';

        $restrictUnit = [
            'PAN 08', 'PAN 04', 'PAN 05', 'PAN 06', '33 MKT', '37 MKT', '45 MKT', '46 MKT', '32 MKT', '41 MKT', '17 MKT', '18 MKT', '20 MKT', '44 MKT'
        ];

        $list = WialonService::GetUnits($token, $restrictUnit);

        if (count($list)) {
            foreach ($list as $key => $item) {


                $rawDate = strtotime(@$item->datetime);

                // convert seconds into a specific format
                $datetime = date("Y-m-d H:i:s", $rawDate);

                $data = [
                    'datetime' => $datetime,
                    'lat' => @$item->lat,
                    'lng' => @$item->lng,
                    'engine' => true,
                    'speed' => @$item->speed,
                    'angle' => 0,
                    'location' => @$item->location,
                ];

                $izzyApi = 'https://connect.izzytrack.com/services';
                $client = new \GuzzleHttp\Client();
                $post = $client->request('POST', $izzyApi, [
                    'json' => [
                        'module' => 'gps.dummy',
                        'token' => '53-7x6a7UFLTKvcaDeAgVQ==',
                        'plate' => @$item->plate,
                        'data' => [
                            'datetime' => $datetime,
                            'lat' => @$item->lat,
                            'lng' => @$item->lng,
                            'engine' => true,
                            'speed' => @$item->speed,
                            'angle' => 0,
                            'location' => @$item->location,
                        ],
                    ]
                ]);

                $response = json_decode($post->getBody()->getContents());

                if ($response->success == false) {
                    logger($item->plate.' asset not found');
                }
            }
        }

        logger('Automation Completed');

        $this->info('Automation successfully');
    }
}
