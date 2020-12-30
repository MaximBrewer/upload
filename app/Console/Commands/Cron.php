<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Cron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:exec';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {


        foreach (glob("/mnt/vol1/www/mp4/*.mp4") as $filename) {
            if(time() - filemtime($filename) > 3600)
                @unlink($filename);
        }


        return 0;
    }
}
