<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Sport\Models\Round;
use Modules\Sport\Models\League;
use Carbon\Carbon;

class LeagueRoundCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demoleagueround:cron';

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
         //\Log::info("leagueround:Cron is working fine!");

       $roundids = Round::where('process_status','pending')->whereDate('start_datetime', '<', Carbon::now())->pluck("id");

       if(count($roundids) > 0){

       $updateRounds = Round::whereIn('id', $roundids)->update(['process_status' => "progress"]);
        
       }

       $updateleague = league::where('process_status','pending')->whereIn('round_to_start', $roundids)->update(['process_status' => "progress"]);
       
       \Log::info($roundids);
    }
}
