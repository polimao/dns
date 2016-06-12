<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\DmsCrawler::class,
        \App\Console\Commands\zhihu2::class,
        \App\Console\Commands\ZhiHuCrawler::class,
        \App\Console\Commands\WeiBoCrawler::class,
        \App\Console\Commands\ZhiHuUserCrawler::class,
        \App\Console\Commands\LaGouCrawler::class,
        \App\Console\Commands\JiaYuanCrawler::class,
        \App\Console\Commands\SiJiaoMaoCrawler::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')
                 ->hourly();
    }
}
