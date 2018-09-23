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
        $token = 'aa8973ca6e3ceb0a9cfa6df027dc11ffF5C47C5913FB976C41861B9BBF96623CC84ADFC2';

        $restrictUnit = [
            'PAN 08', 'PAN 04', 'PAN 05', 'PAN 06', '33 MKT', '37 MKT', '45 MKT', '46 MKT', '32 MKT', '41 MKT', '17 MKT', '18 MKT', '20 MKT', '44 MKT'
        ];

        //$list = WialonService::GetUnits($token, $restrictUnit);

        logger('Test Automation');

        $this->info('Automation successfully');
    }
}
