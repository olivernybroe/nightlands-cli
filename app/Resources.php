<?php

namespace App;

use App\Responses\ResourcesResponse;

class Resources
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function get(): ResourcesResponse
    {
        $response =  $this->client->mutate(
        /** @lang GraphQL */ <<<'GQL'
            query {
              me {
                resources {
                  citizens
                  gold
                }
              }
            }
            GQL,
        );

        return ResourcesResponse::fromResponse($response);
    }
}
