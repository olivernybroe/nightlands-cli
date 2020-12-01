<?php

namespace App;

use App\Responses\AttackResponse;

class Battle
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function attack(int $id): AttackResponse
    {
        $response = $this->client->mutate(
        /** @lang GraphQL */ <<<'GQL'
            mutation Attack($id: ID!){
              actionBattle(input: {id: $id}) {
                attacker {
                  id
                  username
                }
                defender {
                  id
                  username
                }
                defender_casualties {
                  name
                  pivot {
                    quantity
                    initial_quantity
                  }
                }
                attacker_casualties {
                  name
                  pivot {
                    quantity
                    initial_quantity
                  }
                }
                gold_stolen
              }
            }
            GQL,
            [
                'id' => $id,
            ],
        );

        return AttackResponse::fromResponse($response);
    }
}
