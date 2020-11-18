<?php

namespace App\Responses;

use App\Response;

class ResourcesResponse extends Response
{
    public function getCitizens(): int
    {
        return $this->getData()['me']['resources']['citizens'];
    }

    public function getGold(): int
    {
        return $this->getData()['me']['resources']['gold'];
    }
}
