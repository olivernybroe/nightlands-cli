<?php

namespace App\Responses;

use App\Response;

class ScoreboardResponse extends Response
{
    public function getRanks(): array
    {
        return $this->getData()['users']['data'];
    }

    public function getCurrentPage(): int
    {
        return $this->getData()['users']['paginatorInfo']['currentPage'];
    }

    public function getLastPage(): int
    {
        return $this->getData()['users']['paginatorInfo']['lastPage'];
    }

    public function isLastPage(): bool
    {
        return !$this->getData()['users']['paginatorInfo']['hasMorePages'];
    }
}
