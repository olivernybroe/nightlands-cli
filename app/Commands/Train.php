<?php

namespace App\Commands;

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
        // $schedule->command(static::class)->everyMinute();
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
            $choices->all()
        );

        return $units->where('name', Str::title($chosen))->pluck('id')->first();
    }

    private function selectAmount(): int
    {
        return $this->ask('How many? (0=all) ', 0);
    }

    private function train(User $user, int $unit, int $amount): void
    {
        if ($amount === 0) {
            $response = $this->nightlands->resources($user->getLastIssuedToken())->get();
            $amount = $response->getCitizens();
        }

        $this->nightlands->units($user->getLastIssuedToken())->train(
            $unit,
            $amount,
        );

        $this->info("{$amount} Units was successfully queued up for training!");
    }
}
