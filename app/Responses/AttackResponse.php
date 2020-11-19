<?php

namespace App\Responses;

use App\Response;

class AttackResponse extends Response
{
    public function attacker(): string
    {
        return $this->getData()['actionBattle']['attacker']['username'];
    }

    public function defender(): string
    {
        return $this->getData()['actionBattle']['defender']['username'];
    }

    public function defenderCasualties(): array
    {
        return collect($this->getData()['actionBattle']['defender_casualties'])
            ->map(fn(array $casualty) => new Casualty(
                $casualty['name'],
                $casualty['pivot']['initial_quantity'],
                $casualty['pivot']['quantity'],
            ))->all();
    }

    public function attackerCasualties(): array
    {
        return collect($this->getData()['actionBattle']['attacker_casualties'])
            ->map(fn(array $casualty) => new Casualty(
                $casualty['name'],
                $casualty['pivot']['initial_quantity'],
                $casualty['pivot']['quantity'],
            ))->all();
    }
}
