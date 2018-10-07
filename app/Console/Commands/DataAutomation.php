<?php

namespace App\Console\Commands;

use App\Service\WialonService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

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
            '51 MKT', '53 MKT', '61 MKT', '62 MKT', '63 MKT', '64 MKT', '65 MKT', '57 MKT', '59 MKT', '72 MKT'
        ];

        $list = WialonService::GetUnits($token, $restrictUnit);

        if (count($list)) {
            $izzyLog = new Logger('izzy');
            $izzyLog->pushHandler(new StreamHandler(storage_path('logs/izzy.log')), Logger::INFO);

            $izzyLog->info('Start Syncing');

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
                        'module' => 'gps',
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

                $log = '';

                if ($response->success == false) {
                    $log = $item->plate.' asset not found';
                }elseif ($response->success == true){
                    $log = $item->plate.' sync success';
                }

                $izzyLog->info($log);
            }

            $izzyLog->info('Stop Syncing');
        }

        $this->info('Automation successfully');
    }
}
