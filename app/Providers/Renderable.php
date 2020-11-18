<?php


namespace App\Providers;


use Symfony\Component\Console\Output\ConsoleOutput;

interface Renderable
{
    public function render(ConsoleOutput $output): void;
}
