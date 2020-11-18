<?php

namespace App;

class NightLands
{
    public function register(): Registration
    {
        return app(Registration::class);
    }

    public function authentication(): Authentication
    {
        return app(Authentication::class);
    }

    public function scoreBoard(string $token): Scoreboard
    {
        app(Client::class)->setToken($token);
        return app(Scoreboard::class);
    }

    public function units(string $token): Units
    {
        app(Client::class)->setToken($token);
        return app(Units::class);
    }
}
