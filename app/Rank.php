<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property int $rank
 * @property int $id
 */
class Rank extends Model
{
    protected $table = 'scoreboard';

    protected $fillable = [
        'name',
        'rank',
        'id',
    ];

    public function getRank(): int
    {
        return $this->rank;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
