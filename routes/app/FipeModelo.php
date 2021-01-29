<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FipeModelo extends Model
{
    protected $fillable = [
        'id',
        'nome',
        'ano',
        'preco',
        'combustivel',
        'fipe_id',
        'dados',
        'fipe_carro_id',
    ];

    // public static function corrigir()
    // {
    //     $max = FipeModelo::count();
    //     $min = FipeModelo::where('nome', null)->first()->id;

    //     for ($i = $min; $i <= $max; $i++) {
    //         $modelo = FipeModelo::find($i);
    //         $dados = json_decode($modelo->dados, true);
    //         $modelo->nome = $dados['name'];
    //         $modelo->dados = null;
    //         $modelo->save();
    //     }
    // }
}

/*

{"referencia": "abril de 2020", "fipe_codigo": "008030-6", "name": "100 2.8 V6", "combustivel": "Gasolina", "marca": "Audi", "ano_modelo": "1995", "preco": "R$ 12.301,00", "key": "100-1995", "time": 0.010000000000019327, "veiculo": "100 2.8 V6", "id": "1995"}

 */
