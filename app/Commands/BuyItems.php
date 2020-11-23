<?php

namespace App\Commands;

use App\Exceptions\RequestFailed;
use App\MetaInfo;
use App\NightLands;
use App\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Str;

class BuyItems extends Command
{
    protected $signature = 'items:buy {--user=*} {--only-max-conscription}';

    protected $description = 'Buy items in the store';

    public function handle(NightLands $nightLands): void
    {
        $userQuery = $this->option('only-max-conscription')
            ? User::query()->where('conscription_level', '>=', MetaInfo::maxConscriptionLevel()->value)->get()
            : User::all();

        $users = $this->selectUsers($userQuery);
        $item = $this->selectItem($nightLands);
        $amount = $this->selectAmount();

        $users->each(fn(User $user) => $this->buy($nightLands, $user, $item, $amount));
    }

    private function buy(NightLands $nightLands, User $user, array $item, int $amount): void
    {
        $price = $item['price'];
        $gold = $nightLands->resources($user->getLastIssuedToken())->get()->getGold();

        if ($gold < $price) {
            $this->userInfo($user, "Not enough gold to buy.");
            return;
        }

        if ($gold < $price * $amount) {
            $amount = 0;
        }

        if ($amount === 0) {
            $amount = floor($gold / $price);
        }

        try {
            $nightLands->items($user->getLastIssuedToken())->buy(
                $item['id'],
                $amount,
            );

            $this->userNotify($user, "{$amount} of {$item['name']} was successfully bought!");
        } catch (RequestFailed $exception) {
            dump($exception);
            $this->userInfo($user, "Failed buying {$amount}.");
        }
    }

    private function selectItem(NightLands $nightLands): array
    {
        $token = $this->getFirstToken();
        $items = collect($nightLands->items($token)->get()->getItems());

        $choices = $items
            ->sortBy->price
            ->keyBy(fn(array $item) => "id:{$item['id']}")
            ->map(fn(array $item) => Str::lower($item['name']) . " [{$item['price']}]");

        $chosen = $this->choice(
            "Choose a item",
            $choices->all(),
            'id:11'
        );

        return $items->firstWhere('id', Str::after($chosen, 'id:'));
    }

    private function selectAmount(): int
    {
        return $this->ask('How many? (0=all) ', 0);
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        $schedule->command(
            static::class,
            ['--only-max-conscription']
        )->everyThirtyMinutes();
    }
}
