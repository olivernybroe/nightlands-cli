<?php

namespace App\Commands;

use App\Exceptions\RequestFailed;
use App\NightLands;
use App\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Str;

class Train extends Command
{
    protected $signature = 'units:train';

    protected $description = 'Train units';

    private NightLands $nightlands;

    public function handle(NightLands $nightLands): void
    {
        $this->nightlands = $nightLands;

        $users = $this->selectUsers();
        $unit = $this->selectUnit();
        $amount = $this->selectAmount();

        $users->each(fn(User $user) => $this->train($user, $unit, $amount));
    }

    public function schedule(Schedule $schedule): void
    {
        $schedule->command(static::class)->hourly();
    }

    private function selectUnit(): int
    {
        $token = $this->getFirstToken();
        $units = collect($this->nightlands->units($token)->get()->getUnits());

        $choices = $units
            ->keyBy->id
            ->map(fn(array $unit) => Str::lower($unit['name']));

        $chosen = $this->choice(
            "Choose a unit",
            $choices->all(),
            "gold miner",
        );

        return $units->where('name', Str::title($chosen))->pluck('id')->first();
    }

    private function selectAmount(): int
    {
        return $this->ask('How many? (0=all) ', 0);
    }

    private function train(User $user, int $unit, int $amount): void
    {
        $response = $this->nightlands->resources($user->getLastIssuedToken())->get();
        $citizens = $response->getCitizens();

        $amount = $amount === 0 ? $citizens : $amount;

        if ($citizens === 0) {
            $this->userInfo($user, "No citizens to train.");
            return;
        }

        if ($amount > $citizens) {
            $amount = $citizens;
        }

        try {
            $this->nightlands->units($user->getLastIssuedToken())->train(
                $unit,
                $amount,
            );

            $this->userNotify($user, "{$amount} Units was successfully queued up for training!");
        } catch (RequestFailed $exception) {
            dump($exception);
            $this->userInfo($user, "Failed training {$amount}.");
        }
    }
}
