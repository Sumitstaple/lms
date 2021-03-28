<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\ResultCronController;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
   protected $commands = [
        Commands\NotificationCron::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        
        $base_url = "https://app.lastman.com.au/cronhandler/lms_forfeit";

         // $schedule->command('demo:cron')->daily();
         // $schedule->command('demoleagueround:cron')->everyMinute();
         $schedule->call(function () {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://app.lastman.com.au/cronhandler/lms_forfeit");
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_exec($ch);
                curl_close($ch);
            })->everyMinute();

         $schedule->call(function () {

          $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://app.lastman.com.au/cronhandler/lml_forfeit");
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_exec($ch);
                curl_close($ch);
                   
        })->everyMinute();

        $schedule->call(function () {

          $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://app.lastman.com.au/cronhandler/lms_result");
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_exec($ch);
                curl_close($ch);
                   
        })->everyMinute();

        $schedule->call(function () {

          $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://app.lastman.com.au/cronhandler/lml_result");
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_exec($ch);
                curl_close($ch);
                   
        })->everyMinute();

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
