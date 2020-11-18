<?php

namespace App;

use App\Responses\ConscriptionResponse;

class Conscription
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function research(): ConscriptionResponse
    {
        $response = $this->client->mutate(
        /** @lang GraphQL */ <<<'GQL'
            mutation {
              actionResearchConscription {
                conscription {
                  level
                }
                conscription_next_level {
                    level
                }
                conscription_upgrade_finished_at
              }
            }
            GQL,
        );

        return ConscriptionResponse::fromResponse($response);
    }
}
