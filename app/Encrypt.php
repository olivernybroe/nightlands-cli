<?php

namespace App;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Facades\Crypt;

class Encrypt implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        return ! is_null($value) ? Crypt::decryptString($value) : null;
    }

    public function set($model, string $key, $value, array $attributes)
    {

        return [$key => ! is_null($value) ?  Crypt::encryptString($value) : null];
    }
}
