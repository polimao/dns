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
        \App\Console\Commands\Dms::class,
        \App\Console\Commands\zhihu2::class,
        \App\Console\Commands\CrawlerZhiHu::class,
        \App\Console\Commands\CrawlerWeiBo::class,
        \App\Console\Commands\CrawlerZhiHuUser::class,
        \App\Console\Commands\CrawlerLaGou::class,
        \App\Console\Commands\JiaYuanCrawler::class,
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
