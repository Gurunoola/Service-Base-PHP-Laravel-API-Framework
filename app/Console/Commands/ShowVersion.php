<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ShowVersion extends Command
{
    protected $signature = 'app:version';
    protected $description = 'Display the application version';

    public function handle()
    {
        $version = json_decode(file_get_contents(base_path('composer.json')))->version;
        $this->info("Application Version: {$version}");
    }
}