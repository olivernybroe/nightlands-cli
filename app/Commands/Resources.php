<?php

namespace App\Commands;

use App\NightLands;
use App\User;
use Illuminate\Console\Scheduling\Schedule;

class Resources extends Command
{
    protected $signature = 'resources';

    protected $description = 'Shows a users resources.';

    public function handle(NightLands $nightLands): void
    {
        $users = $this->selectUsers();

        $entries = $users->map(static function (User $user) use ($nightLands) {
            $response = $nightLands->resources($user->getLastIssuedToken())->get();
            return [$user->getDisplayName(), $response->getCitizens(), $response->getGold()];
        });

        $this->table(
            ['User', 'Citizens', 'Gold'],
            $entries
        );
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
