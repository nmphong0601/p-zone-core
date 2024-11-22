<?php

namespace PZone\Core\Commands;

use Illuminate\Console\Command;
use Throwable;
use DB;
use Illuminate\Support\Facades\Artisan;

class Update extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pz:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update P-Zone core"';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            Artisan::call('db:seed', 
                [
                    '--class' => 'DataDefaultSeeder',
                    '--force' => true
                ]
            );
            Artisan::call('db:seed', 
                [
                    '--class' => 'DataLocaleSeeder',
                    '--force' => true
                ]
            );
            $this->info('- Update database done!');
        } catch (Throwable $e) {
            pz_report($e->getMessage());
            echo  json_encode(['error' => 1, 'msg' => $e->getMessage()]);
            exit();
        }
        $this->info('---------------------');
        $this->info('Front version: '.config('p-zone.version'));
        $this->info('Front sub-version: '.config('p-zone.sub-version'));
        $this->info('Core: '.config('p-zone.core'));
        $this->info('Core sub-version: '.config('p-zone.core-sub-version'));
    }
}
