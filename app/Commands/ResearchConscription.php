<?php

namespace App\Commands;

use App\Exceptions\RequestFailed;
use App\MetaInfo;
use App\NightLands;
use App\User;
use Illuminate\Console\Scheduling\Schedule;

class ResearchConscription extends Command
{
    protected $signature = 'conscription:research';

    protected $description = 'Research the next level of conscription.';

    public function handle(NightLands $nightLands): void
    {
        $users = $this->selectUsers(
            User::query()->where(
                'conscription_level',
                '<',
                (int) MetaInfo::maxConscriptionLevel()->value
            )->orWhereNull('conscription_level')
            ->get()
        );

        $users->each(function (User $user) use ($nightLands) {
            try {
                $response = $nightLands->conscription($user->getLastIssuedToken())->research();
            } catch (RequestFailed $exception) {
                $this->userInfo($user, "Failed researching next conscription level.");
                return;
            }

            $user->update([
                'conscription_upgrade_finished_at' => $response->upgradeFinishedAt(),
                'conscription_level' => $response->nextLevel(),
            ]);

            $this->userNotify(
                $user,
                <<<TXT
                    Upgrading to level {$response->nextLevel()}.
                    "Finished at: {$response->upgradeFinishedAt()->toDateTimeString()}.
                    TXT
            );
        });
    }

    public function schedule(Schedule $schedule): void
    {
        $schedule->command(static::class)->hourly();
    }
}
