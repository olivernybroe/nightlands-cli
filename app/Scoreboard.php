<?php

namespace App;

use App\Responses\ScoreboardResponse;

class Scoreboard
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function get(int $count = 25, int $page = 1): ScoreboardResponse
    {
        $response = $this->client->query(
            /** @lang GraphQL */ <<<'GQL'
            query Scoreboard($first: Int! $page: Int!){
              users(first: $first page: $page) {
                paginatorInfo {
                  lastPage
                  currentPage
                  hasMorePages
                }
                data {
                  ranking
                  username
                  id
                }
              }
            }
            GQL,
            [
                'first' => $count,
                'page' => $page,
            ],
        );

        return ScoreboardResponse::fromResponse($response);
    }
}
