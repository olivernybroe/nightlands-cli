<?php

namespace App\Commands;

use App\NightLands;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;

class Units extends Command
{
    protected $signature = 'units';

    protected $description = 'List all units';

    public function handle(NightLands $nightLands): void
    {
        $token = $this->getFirstToken();

        $units = collect($nightLands->units($token)->get()->getUnits());
        $units = $units->groupBy('type')
            ->map(fn(Collection $type) => $type->map(function (array $unit) {
                return [
                    'id' => $unit['id'],
                    'name' => $unit['name'],
                    'type' => strtolower($unit['type']),
                ];
            }))
            ->flatMap(fn(Collection $type) => $type->add(new TableSeparator()));
        $units->pop();

        $table = new Table($this->output);
        $table->setHeaderTitle('Units')
            ->setHeaders(['id', 'name', 'type'])
            ->setRows($units->all());
        $table->render();
    }

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
