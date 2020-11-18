<?php

namespace App;

use App\Exceptions\RequestFailed;
use GuzzleHttp\Client as GuzzleClient;

class Client
{
    private string $uri = "https://api.nightlands.app/graphql";
    private ?string $token = null;
    private GuzzleClient $guzzle;

    public function __construct(GuzzleClient $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    public function query(string $query, array $args = []): Response
    {
        return $this->request($query, $args);
    }

    public function mutate(string $mutation, array $args = []): Response
    {
        return $this->request($mutation, $args);
    }

    public function setToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    private function request(string $query, array $args): Response
    {
        $response = $this->guzzle->post($this->uri, [
            'headers' => [
                'User-Agent' => 'Nightlands CLI/0.1',
                'Authorization' => "Bearer {$this->token}"
            ],
            'json' => [
                'query' => $query,
                'variables' => $args,
            ]
        ])->getBody()->__toString();
        $json = \Safe\json_decode($response, true);

        if (isset($json['errors'])) {
            throw new RequestFailed($json['errors']);
        }

        return new Response($json);
    }
}
