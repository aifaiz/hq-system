<?php

namespace App\Console\Commands;

use App\Models\AgentUser;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpiredAgent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expired-agent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable expired agent user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        AgentUser::whereDate('lp_expire', $today->format('Y-m-d'))->update(['status', '0']);
    }
}
