<?php

namespace App\Responses;

use App\Response;

class UnitsResponse extends Response
{
    public function getUnits(): array
    {
        return $this->getData()['units'];
    }
}
