<?php

namespace App;

use App\Responses\ItemsResponse;

class Items
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function get(): ItemsResponse
    {
        $response =  $this->client->query(
        /** @lang GraphQL */ <<<'GQL'
            query {
              items {
                id
                name
                price
              }
            }
            GQL,
        );

        return ItemsResponse::fromResponse($response);
    }

    public function buy(int $id, int $amount = 1): Response
    {
        return $this->client->mutate(
        /** @lang GraphQL */ <<<'GQL'
            mutation Buy($id: ID!, $amount: Int!) {
              actionBuyItems(
                input: {
                    items: [
                      {
                        id: $id
                        quantity: $amount
                      }
                    ]
                }
              ) {
                id
              }
            }
            GQL,
            [
                'id' => $id,
                'amount' => $amount,
            ],
        );
    }
}
