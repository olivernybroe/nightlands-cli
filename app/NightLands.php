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

    public function resources(string $token): Resources
    {
        app(Client::class)->setToken($token);
        return app(Resources::class);
    }

    public function conscription(string $token): Conscription
    {
        app(Client::class)->setToken($token);
        return app(Conscription::class);
    }

    public function battle(string $token): Battle
    {
        app(Client::class)->setToken($token);
        return app(Battle::class);
    }

    public function items(string $token): Items
    {
        app(Client::class)->setToken($token);
        return app(Items::class);
    }
}
