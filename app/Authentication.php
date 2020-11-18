<?php

namespace App;

use App\Responses\LoginTokenResponse;
use App\Responses\RefreshTokenResponse;

class Authentication
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function refreshToken(string $token): RefreshTokenResponse
    {
        $this->client->setToken($token);

        $response = $this->client->mutate(
        /** @lang GraphQL */ <<<'GQL'
            mutation RefreshToken {
              refreshToken
            }
            GQL,
        );

        return RefreshTokenResponse::fromResponse($response);
    }

    public function logIn(string $email, string $password, int $ttl = 10_080): LoginTokenResponse
    {
        $response = $this->client->mutate(
        /** @lang GraphQL */ <<<'GQL'
            mutation LogIn($email: String!, $password: String!, $ttl: Int!) {
              logIn(
                input: {
                  email: $email
                  password: $password
                  ttl: $ttl
                }
              )
            }
            GQL,
            [
                'email' => $email,
                'password' => $password,
                'ttl' => $ttl,
            ],
        );

        return LoginTokenResponse::fromResponse($response);
    }
}
