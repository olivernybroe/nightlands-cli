<?php

namespace App\Commands;

use App\NightLands;
use App\User;
use Illuminate\Console\Scheduling\Schedule;

class SetpUsername extends Command
{
    protected $signature = 'auth:username';

    protected $description = 'Define a username for a user.';

    public function handle(NightLands $nightLands): void
    {
        $users = $this->selectUsers(
            User::query()->whereNull('username')->get()
        );

        $users->each(function (User $user) use ($nightLands) {
            $nightLands->register()->setupUsername(
                $user->getLastIssuedToken(),
                $username = $this->ask("Enter username for {$user->getEmail()}")
            );

            $user->update([
                'username' => $username,
            ]);
        });
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
