<?php

namespace App\Commands;

use App\NightLands;
use App\User;
use Illuminate\Support\Collection;

class Login extends Command
{
    protected $signature = 'auth:login';

    protected $description = 'Logs in a user.';

    private NightLands $nightlands;

    public function handle(NightLands $nightLands): void
    {
        $this->nightlands = $nightLands;

        $users = $this->selectUsers();

        $this->logIn($users);
    }

    private function logIn(Collection $users): void
    {
        $this->output->progressStart($users->count());

        $users->each(function (User $user) {
            $response = $this->nightlands->authentication()->logIn(
                $user->getEmail(),
                $user->getPassword()
            );
            $user->update([
                'last_issued_token' => $response->getToken()
            ]);
            $this->output->progressAdvance();
        });

        $this->output->progressFinish();
    }
}
