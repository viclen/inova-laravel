<?php

namespace App\Http\Controllers;

use App\Carro;
use App\Marca;
use App\Modelo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FipeController extends Controller
{
    public function index()
    {
        $carros = Carro::with('marca')->orderByDesc('uso')->get();
        $marcas = Marca::all();

        return view('pages.fipe.index', [
            'carros' => $carros,
            'marcas' => $marcas
        ]);
    }

    public function show($carro_id)
    {
        Carro::where('id', $carro_id)->update(['uso' => DB::raw('uso + 1')]);
        return Modelo::where('carro_id', $carro_id)->get();
    }

    public function store(Request $request)
    {
        $carro_id = $request["carro"];
        $marca_id = $request["marca"];
        $preco = $request["preco"];
        $comparador = $request["comparador"];
        $ano = $request["ano"];
        $modelo = $request["modelo"];

        if ($carro_id) {
            $query = Modelo::where('carro_id', $carro_id);
        } elseif ($marca_id) {
            $query = Modelo::with(['carro' => function ($query) use ($marca_id) {
                $query->where('marca_id', $marca_id);
            }]);
        } else {
            return abort(401, 'Selecione a marca ou carro');
        }

        if ($preco) {
            $query->where(
                function ($query) use ($preco, $comparador) {
                    if ($comparador == 'maior') {
                        $query->where('preco', '>', $preco);
                    } elseif ($comparador == 'menor') {
                        $query->where('preco', '<', $preco);
                    } else {
                        $query->where('preco', $preco);
                    }
                }
            );
        }
        if ($ano) {
            $query->where('ano', $ano);
        }
        if ($modelo) {
            $modelo = Modelo::find($modelo);
            $query->where('nome', 'like', "%$modelo->nome%");
        }

        return $query->where('nome', '!=', '')->get();
    }
}
