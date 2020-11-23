<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property string $value
 */
class MetaInfo extends Model
{
    protected $table = 'meta_info';

    protected $fillable = [
        'name',
        'value',
    ];

    public static function maxConscriptionLevel(): MetaInfo
    {
        return self::where('name', 'MaxConscriptionLevel')->firstOrNew([
            'name' => 'MaxConscriptionLevel'
        ]);
    }
}
