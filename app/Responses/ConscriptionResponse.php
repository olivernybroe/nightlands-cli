<?php

namespace App\Responses;

use App\Response;
use Carbon\Carbon;

class ConscriptionResponse extends Response
{
    public function upgradeFinishedAt(): Carbon
    {
        return Carbon::parse($this->getData()['actionResearchConscription']['conscription_upgrade_finished_at']);
    }

    public function currentLevel(): int
    {
        return $this->getData()['actionResearchConscription']['conscription']['level'];
    }

    public function nextLevel(): int
    {
        return $this->getData()['actionResearchConscription']['conscription_next_level']['level'];
    }
}
