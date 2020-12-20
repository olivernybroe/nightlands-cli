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
        $schedule->command(static::class)->hourlyAt(5);
    }

    private function selectUnit(): array
    {
        $token = $this->getFirstToken();
        $units = collect($this->nightlands->units($token)->get()->getUnits());

        $choices = $units
            ->keyBy->id
            ->map(fn(array $unit) => Str::lower($unit['name']));

        $chosen = $this->choice(
            "Choose a unit",
            $choices->all(),
            "crossbowman",
        );

        return $units->firstWhere('name', Str::title($chosen));
    }

    private function selectAmount(): int
    {
        return $this->ask('How many? (0=all) ', 0);
    }

    private function train(User $user, array $unit, int $amount): void
    {
        $response = $this->nightlands->resources($user->getLastIssuedToken())->get();
        $citizens = $response->getCitizens();
        $gold = $response->getGold();

        $amount = $amount === 0 ? $citizens : $amount;

        if ($citizens === 0) {
            $this->userInfo($user, "No citizens to train.");
            return;
        }

        if ($amount > $citizens) {
            $amount = $citizens;
        }

        // Buy items
        try {
            collect($unit['items'])
                ->keyBy('id')
                ->map(fn(array $item) => $item['price'] * $amount)
                ->filter(fn(int $price) => $gold < $price)
                ->each(fn(int $price, int $itemId) => $this->nightlands->items($user->getLastIssuedToken())->buy(
                    $itemId,
                    $amount
                ));
        } catch (RequestFailed $exception) {
            $this->userInfo($user, "Failed buying all items needed.");
        }

        try {
            $this->nightlands->units($user->getLastIssuedToken())->train(
                (int) $unit['id'],
                $amount,
            );

            $this->userInfo($user, "{$amount} Units queued for training!");
        } catch (RequestFailed $exception) {
            dump($exception);
            $this->userInfo($user, "Failed training {$amount}.");
        }
    }
}
