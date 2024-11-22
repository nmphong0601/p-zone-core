<?php

namespace PZone\Core\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Throwable;

class Infomation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pz:info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get infomation P-Zone';
    const LIMIT = 10;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info(config('p-zone.name').' - '.config('p-zone.title'));
        $this->info(config('p-zone.auth').' <'.config('p-zone.email').'>');
        $this->info('Front version: '.config('p-zone.version'));
        $this->info('Front sub-version: '.config('p-zone.sub-version'));
        $this->info('Core: '.config('p-zone.core'));
        $this->info('Core sub-version: '.config('p-zone.core-sub-version'));
        $this->info('Type: '.config('p-zone.type'));
        $this->info('Homepage: '.config('p-zone.homepage'));
        $this->info('Github: '.config('p-zone.github'));
        $this->info('Facebook: '.config('p-zone.facebook'));
        $this->info('API: '.config('p-zone.api_link'));
    }
}
