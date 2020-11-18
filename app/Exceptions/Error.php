<?php

namespace App\Exceptions;

class Error
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getMessage(): string
    {
        return $this->data['message'];
    }

    public function getExtensions(): array
    {
        return $this->data['extensions'];
    }
}
