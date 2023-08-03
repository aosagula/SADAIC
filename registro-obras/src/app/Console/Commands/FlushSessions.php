<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class FlushSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Borra las sessions';

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
        $driver = config('session.driver');

        switch($driver) {
            case 'file':
                $path = config('session.files');
        
                if (File::exists($path)) {
                    $files = File::allFiles($path);
                    File::delete($files);
                    error_log( count($files).' sessions flushed');
                }
            break;
        }

        return 0;
    }
}
