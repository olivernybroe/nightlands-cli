<?php

namespace App\Commands;

use App\NightLands;
use App\User;

class AddUser extends Command
{
    protected $signature = 'auth:add';

    protected $description = 'Add an already existing user';

    public function handle(NightLands $nightLands): void
    {
        /** @var User $user */
        $user = User::query()->create([
            'email' => $this->ask('Enter email address'),
            'password' => $this->ask('Enter password'),
        ]);

        $response = $nightLands->authentication()->logIn(
            $user->getEmail(),
            $user->getPassword(),
        );
        $user->update([
            'last_issued_token' => $response->getToken()
        ]);

        $this->info('User was successfully added!');
    }
}
