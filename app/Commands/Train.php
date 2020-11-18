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
        $amount = $this->ask('How many?', 1);

        $users->each(fn(User $user) => $this->nightlands->units($user->getLastIssuedToken())->train(
            $unit,
            $amount,
        ));
        $this->info("Units was successfully queued up for training!");
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
}
