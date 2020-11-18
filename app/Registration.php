<?php

namespace App;

class Registration
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function viaEmail(string $email, string $password): Response
    {
        return $this->client->mutate(
            /** @lang GraphQL */ <<<'GQL'
            mutation Register($email: String!, $password: String!) {
              register(
                input: {
                  email: $email
                  password: $password
                }
              )
            }
            GQL,
            [
                'email' => $email,
                'password' => $password,
            ],
        );
    }
}
