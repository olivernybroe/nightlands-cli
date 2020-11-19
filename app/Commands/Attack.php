<?php

namespace App\Commands;

use App\NightLands;
use App\Rank;
use App\Responses\Casualty;
use App\User;
use Illuminate\Console\Scheduling\Schedule;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;

class Attack extends Command
{
    protected $signature = 'battle:attack';

    protected $description = 'Attack another user';

    public function handle(NightLands $nightLands): void
    {
        $users = $this->selectUsers();

        $attack = $this->choice(
            "Select user to attack [rank] => name",
            Rank::query()->orderBy('rank')->get()
            ->keyBy->getRank()
            ->map->getName()
            ->all(),
        );
        /** @var Rank $attack */
        $attack = Rank::query()->where('name', $attack)->first();

        $this->info("Attacking {$attack->getName()} [id:{$attack->getKey()}] [rank:{$attack->getRank()}]");

        $users->each(function (User $user) use ($nightLands) {
            $response = $nightLands->battle($user->getLastIssuedToken())->attack(37);

            $table = new Table($this->output);

            $defenderCasualties = collect($response->defenderCasualties())->map(fn(Casualty $casualty) => [
                $casualty->getName(),
                $casualty->getKilled(),
                $casualty->getInitial()
            ]);

            $attackerCasualties = collect($response->attackerCasualties())->map(fn(Casualty $casualty) => [
                $casualty->getName(),
                $casualty->getKilled(),
                $casualty->getInitial()
            ]);

            $table->setHeaderTitle($user->getDisplayName())
                ->setHeaders(['Unit', 'Killed', 'Initial'])
                ->addRows($defenderCasualties->all())
                ->addRow(new TableSeparator())
                ->addRows($attackerCasualties->all());
            $table->render();
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
