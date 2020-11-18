<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string      $email
 * @property string|null $username
 * @property string      $password
 * @property string|null $last_issued_token
 */
class User extends Model
{
    protected $fillable = [
        'email',
        'password',
        'last_issued_token',
        'username',
    ];

    protected $hidden = [
        'password',
        'last_issued_token',
    ];

    protected $casts = [
        'password' => Encrypt::class,
    ];

    public function getDisplayName(): string
    {
        return $this->getUsername() ?? $this->getEmail();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getLastIssuedToken(): ?string
    {
        return $this->last_issued_token;
    }
}
