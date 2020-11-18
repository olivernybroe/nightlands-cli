<?php

namespace App\Commands;

use App\NightLands;
use App\User;
use Illuminate\Console\Scheduling\Schedule;

class Register extends Command
{
    protected $signature = 'auth:register';

    protected $description = 'Register a new account';

    public function handle(NightLands $nightLands): void
    {
        $nightLands->register()->viaEmail(
            $email = $this->ask("Enter email address"),
            $password = $this->ask("Enter password")
        );

        $token = $nightLands->authentication()->logIn(
            $email,
            $password
        )->getToken();

        if ($this->confirm("Save user in the database? (password is encrypted, not hashed)", true)) {
            User::query()->create([
                'email' => $email,
                'password' => $password,
                'last_issued_token' => $token
            ]);
        }

        $this->info("Your account has been created and a verification email has been sent!");
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
