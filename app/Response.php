<?php

namespace App;

class Response
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data['data'];
    }

    public function getRaw(): array
    {
        return $this->data;
    }

    public static function fromResponse(Response $response)
    {
        return new static($response->getRaw());
    }
}
