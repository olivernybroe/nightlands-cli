<?php

namespace App\Commands;

use App\Exceptions\RequestFailed;
use App\NightLands;
use App\Rank;
use App\Responses\Casualty;
use App\User;
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
        );
        /** @var Rank $attack */
        $attack = Rank::query()->where('name', $attack)->first();

        $this->info("Attacking {$attack->getName()} [id:{$attack->getKey()}] [rank:{$attack->getRank()}]");

        $casualties = $users->map(function (User $user) use ($nightLands, $attack) {
            try {
                $response = $nightLands->battle($user->getLastIssuedToken())->attack($attack->getKey());
            } catch (RequestFailed $exception) {
                dump($exception->getErrors());
                $this->userInfo($user, "Failed attacking.");
                return null;
            }

            $this->userInfo($user, "Successfully attacked!");

            return collect($response->defenderCasualties());
        })->filter()
            ->flatten(1)
            ->groupBy->getName()
            ->map(fn(Collection $unitCasualties) => [
                optional($unitCasualties->first())->getName(),
                $unitCasualties->sum(fn(Casualty $casualty) => $casualty->getKilled())
            ]);

        $table = new Table($this->output);

        $table->setHeaderTitle($attack->getName())
            ->setHeaders(['Unit', 'Killed'])
            ->addRows($casualties->all());
        $table->render();
    }
}
