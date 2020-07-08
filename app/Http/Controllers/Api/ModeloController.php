<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modelo;
use Illuminate\Http\Request;

class ModeloController extends Controller
{
    public function index()
    {
        $carro_id = request()->input("carro");
        $marca_id = request()->input("marca");

        $preco = request()->input("preco");
        $comparador = request()->input("comparador");

        if ($carro_id) {
            $query = Modelo::where('carro_id', $carro_id);
        } elseif ($marca_id) {
            $query = Modelo::with(['carro' => function ($query) use ($marca_id) {
                $query->where('marca_id', $marca_id);
            }]);
        } else {
            return abort(401, 'Selecione a marca ou carro');
        }

        return $query->where(function ($query) use ($preco, $comparador) {
            if ($preco) {
                if ($comparador == 'maior') {
                    $query->where('preco', '>', $preco);
                } elseif ($comparador == 'menor') {
                    $query->where('preco', '<', $preco);
                } else {
                    $query->where('preco', $preco);
                }
            }
        })->get();
    }

    public function show($id)
    {
        return Modelo::find($id);
    }
}
