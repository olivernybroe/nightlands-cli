<?php

namespace App\Commands;

use App\MetaInfo;
use App\NightLands;
use Illuminate\Console\Scheduling\Schedule;

class ListConscription extends Command
{
    protected $signature = 'conscription:list';

    protected $description = 'Show current level of conscription.';

    public function handle(NightLands $nightLands): void
    {
        $token = $this->getFirstToken();

        $levels = $nightLands->conscription($token)->list()
            ->getData()['conscriptionLevels'];

        $this->table(
            ['Level', 'Name', 'Cost'],
            $levels,
        );

        $last = collect($levels)->last(fn(array $level) => $level['level']);
        MetaInfo::maxConscriptionLevel()->fill([
            'value' => $last['level'],
        ])->save();
    }

    public function schedule(Schedule $schedule): void
    {
        $schedule->command(static::class)->daily();
    }
}
