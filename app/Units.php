<?php

namespace App;

use App\Responses\UnitsResponse;

class Units
{
    private const MINER = 11;

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function get(): UnitsResponse
    {
        $response = $this->client->mutate(
        /** @lang GraphQL */ <<<'GQL'
            query Units {
              units {
                id
                name
                category
                type
                items {
                  id
                  name
                  price
                }
              }
            }
            GQL,
        );

        return UnitsResponse::fromResponse($response);
    }

    public function train(int $id, int $amount = 1): Response
    {
        return $this->client->mutate(
            /** @lang GraphQL */ <<<'GQL'
            mutation Train($id: ID!, $amount: Int!) {
              actionTrainUnit(
                input: {
                  id: $id
                  quantity: $amount
                }
              ) {
                unit {
                   name
                }
                finished_at
              }
            }
            GQL,
            [
                'id' => $id,
                'amount' => $amount,
            ],
        );
    }

    public function trainMiner(int $amount = 1): Response
    {
        return $this->train(self::MINER, $amount);
    }
}
