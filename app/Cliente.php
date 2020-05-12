<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cliente extends Model
{
    public $types;

    protected $fillable = [
        'nome',
        'telefone',
        'endereco',
        'cidade',
        'email',
        'cpf'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'pivot'
    ];

    public function carros()
    {
        return $this->belongsToMany(Carro::class, 'carro_clientes');
    }

    public function interesses()
    {
        return $this->hasMany(Interesse::class);
    }

    public function getTypes()
    {
        $types = [];
        $items = DB::select('describe ' . $this->getTable());

        foreach ($items as $item) {
            $types[$item->Field] = $item->Type;
        }

        return $types;
    }
}
