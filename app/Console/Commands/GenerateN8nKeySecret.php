<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateN8nKeySecret extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-n8n-key-secret';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera un secret key para la comunicacion n8n -> api';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $key = Str::random(60); 
        echo 'sk_n8n_'.$key;
    }
}
