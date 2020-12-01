<?php

namespace App\Commands;

use App\NightLands;
use App\User;
use Illuminate\Console\Scheduling\Schedule;

class TokensRefresh extends Command
{
    protected $signature = 'auth:refresh';

    protected $description = 'Refreshes all tokens';

    public function handle(NightLands $nightLands): void
    {
        $users = $this->selectUsers(User::query()->whereNotNull('last_issued_token')->get());

        $this->output->progressStart($users->count());

        $users->each(function (User $user) use ($nightLands) {
            $response = $nightLands->authentication()->refreshToken($user->getLastIssuedToken());
            $user->update([
                'last_issued_token' => $response->getToken(),
            ]);
            $this->output->progressAdvance();
        });

        $this->output->progressFinish();
        $this->notify("Refreshed all tokens.");
    }

    public function schedule(Schedule $schedule): void
    {
        $schedule->command(static::class)->twiceDaily();
    }
}
