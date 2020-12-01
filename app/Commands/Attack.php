<?php

namespace App\Commands;

use App\Exceptions\RequestFailed;
use App\NightLands;
use App\Rank;
use App\Responses\Casualty;
use App\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Helper\Table;

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
            Rank::query()->orderBy('rank')->first()->name
        );
        /** @var Rank $attack */
        $attack = Rank::query()->where('name', $attack)->first();

        $this->info("Attacking {$attack->getName()} [id:{$attack->getKey()}] [rank:{$attack->getRank()}]");

        $goldStolen = 0;
        $casualties = $users->map(function (User $user) use ($nightLands, $attack, &$goldStolen) {
            try {
                $response = $nightLands->battle($user->getLastIssuedToken())->attack($attack->getKey());
            } catch (RequestFailed $exception) {
                dump($exception->getErrors());
                $this->userInfo($user, "Failed attacking.");
                return null;
            }

            $this->userInfo($user, "Successfully attacked!");

            $goldStolen += $response->goldStolen();
            return collect($response->defenderCasualties());
        })->filter()
            ->flatten(1)
            ->groupBy->getName()
            ->map(fn(Collection $unitCasualties) => [
                optional($unitCasualties->first())->getName(),
                $unitCasualties->sum(fn(Casualty $casualty) => $casualty->getKilled())
            ]);

        $table = new Table($this->output);

        $this->info("Stole $goldStolen gold.");
        $table->setHeaderTitle($attack->getName())
            ->setHeaders(['Unit', 'Killed'])
            ->addRows($casualties->all());
        $table->render();
    }

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyFourHours();
    }
}
