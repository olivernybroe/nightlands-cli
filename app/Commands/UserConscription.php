<?php

namespace App\Commands;

use App\MetaInfo;
use App\NightLands;
use App\User;
use Illuminate\Console\Scheduling\Schedule;

class UserConscription extends Command
{
    protected $signature = 'conscription:users';

    protected $description = 'Show current level of conscription for users.';

    public function handle(NightLands $nightLands): void
    {
        $users = $this->selectUsers();

        $highestLevel = (int) MetaInfo::maxConscriptionLevel()->value;
        $levels = $users->map(function (User $user) use ($nightLands, $highestLevel) {
            if ($user->conscription_level >= $highestLevel) {
                return  [$user->getDisplayName(), $user->conscription_level];
            }


            $response = $nightLands->conscription($user->getLastIssuedToken())->get();
            $level = $response->getData()['me']['conscription']['level'];

            $user->update([
                'conscription_level' => $level,
            ]);

            return [$user->getDisplayName(), $level];
        });

        $this->table(
            ['User', 'Level'],
            $levels->all()
        );
    }

    public function schedule(Schedule $schedule): void
    {
        $schedule->command(static::class)->daily();
    }
}
