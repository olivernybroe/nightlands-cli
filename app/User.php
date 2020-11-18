<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string      $email
 * @property string|null $username
 * @property string      $password
 * @property string|null $last_issued_token
 * @property Carbon|null $conscription_upgrade_finished_at
 */
class User extends Model
{
    protected $fillable = [
        'email',
        'password',
        'last_issued_token',
        'username',
        'conscription_upgrade_finished_at'
    ];

    protected $hidden = [
        'password',
        'last_issued_token',
    ];

    protected $casts = [
        'password' => Encrypt::class,
    ];

    protected $dates = [
        'conscription_upgrade_finished_at',
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
