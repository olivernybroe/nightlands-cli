<?php

namespace App\Commands;

use App\User;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;

class NotifyConscription extends Command
{
    protected $signature = 'conscription:check';

    protected $description = 'Checks when conscription finishes';

    public function handle()
    {
        $users = User::query()
            ->whereDate('conscription_upgrade_finished_at', Carbon::now())
            ->whereTime('conscription_upgrade_finished_at', '>', Carbon::now())
            ->whereTime('conscription_upgrade_finished_at', '<', Carbon::now()->addMinutes(1))
            ->get();

        $users->each(function (User $user) {
            Log::info("Conscription notification send.");
            $this->notify(
                "Conscription upgrade finishes at {$user->conscription_upgrade_finished_at->format('H:i:s')}."
            );
        });
    }

    public function schedule(Schedule $schedule): void
    {
        $schedule->command(static::class)->everyMinute();
    }
}
