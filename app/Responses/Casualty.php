<?php

namespace App\Responses;

class Casualty
{
    private string $name;
    private int $initial;
    private int $killed;

    public function __construct(string $name, int $initial, int $killed)
    {
        $this->name = $name;
        $this->initial = $initial;
        $this->killed = $killed;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getInitial(): int
    {
        return $this->initial;
    }

    public function getKilled(): int
    {
        return $this->killed;
    }
}
