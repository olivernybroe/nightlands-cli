<?php

namespace App\Responses;

use App\Response;

class RefreshTokenResponse extends Response implements TokenResponse
{
    public function getToken(): string
    {
        return $this->getData()['refreshToken'];
    }
}
