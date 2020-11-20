<?php

namespace App\Responses;

use App\Response;

class ItemsResponse extends Response
{
    public function getItems(): array
    {
        return $this->getData()['items'];
    }
}
