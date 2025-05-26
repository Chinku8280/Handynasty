<?php

namespace App\Console;

use App\Console\Commands\ChangeModulePermissions;
use App\Console\Commands\LoyaltyPointsExpire;
use App\Console\Commands\LoyaltyProgramExpire;
use App\Console\Commands\VendorCleanUpCommand;
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
        // ChangeModulePermissions::class,
        LoyaltyPointsExpire::class,
        LoyaltyProgramExpire::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        $schedule->command('loyaltyPointExp:LoyaltyPointsExpire')->dailyAt('00:01');
        $schedule->command('loyaltyProgramExp:LoyaltyProgramExpire')->dailyAt('00:01');

        // $schedule->command('loyaltyPointExp:LoyaltyPointsExpire')->everyMinute();
        // $schedule->command('loyaltyProgramExp:LoyaltyProgramExpire')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
