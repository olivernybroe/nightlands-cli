<?php

namespace App\Commands;

use App\NightLands;
use App\Rank;
use App\User;
use Illuminate\Console\Scheduling\Schedule;
use Symfony\Component\Console\Helper\Table;

class Scoreboard extends Command
{
    protected $signature = 'scoreboard';

    protected $description = 'Show the scoreboard';

    public function handle(NightLands $nightLands): void
    {
        $token = User::query()->whereNotNull('last_issued_token')
            ->pluck('last_issued_token')
            ->first();

        $page = 1;
        do {
            $response = $nightLands->scoreBoard($token)->get(25, $page++);

            collect($response->getRanks())->each(fn(array $rank) => Rank::query()->updateOrCreate(
                [
                    'id' => $rank['id'],
                ],
                [
                    'name' => $rank['username'],
                    'rank' => $rank['ranking'],
                ]
            ));

            $table = new Table($this->output);
            $table
                ->setHeaderTitle("Scoreboard")
                ->setHeaders(['rank', 'username', 'id'])
                ->setRows($response->getRanks())
                ->setFooterTitle("page {$response->getCurrentPage()}/{$response->getLastPage()}");

            $table->render();

            $isLastPage = $response->isLastPage();
        } while (!$isLastPage && $this->confirm('Next page?', true));
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        $schedule->command(static::class)->everyTwoHours();
    }
}
