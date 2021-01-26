<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Carro extends Model
{
    protected $fillable = [
        'nome',
        'fipe_id',
        'marca_id',
        'categoria_id'
    ];

    protected $hidden = [
        'pivot',
        'uso'
    ];

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function categoria()
    {
        return $this->belongsTo(OpcaoCaracteristica::class, "categoria_id", "ordem_id")->where('caracteristica_id', 2);
    }

    public function estoques()
    {
        return $this->hasMany(Estoque::class);
    }

    public function interesses()
    {
        return $this->hasMany(Interesse::class);
    }

    public function modelos()
    {
        return $this->hasMany(Modelo::class);
    }

    public function getTypes()
    {
        $types = [];
        $items = DB::select('describe ' . $this->getTable());

        foreach ($items as $item) {
            if (strpos($item->Field, "fipe") === false) {
                $types[$item->Field] = $item->Type;
            }
        }

        return $types;
    }
}
