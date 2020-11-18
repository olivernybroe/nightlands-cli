<?php

namespace App\Responses;

use App\Response;

class LoginTokenResponse extends Response implements TokenResponse
{
    public function getToken(): string
    {
        return $this->getData()['logIn'];
    }
}
