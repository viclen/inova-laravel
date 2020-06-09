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
        'updated_at',
        'pivot'
    ];

    public function carros()
    {
        return $this->hasMany(CarroCliente::class);
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
