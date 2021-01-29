<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FipeMarca extends Model
{
    protected $fillable = [
        'id',
        'nome',
        'key'
    ];
}
