<?php

namespace App\Http\Controllers;

use App\Carro;
use App\Estoque;
use App\Interesse;
use App\Regra;
use Illuminate\Http\Request;

class InteresseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Interesse  $interesse
     * @return \Illuminate\Http\Response
     */
    public function show(Interesse $interesse)
    {
        return $interesse;

        // $interesse = Interesse::find($id);

        // $regras = Regra::all();

        // $carrosencontrados = [];
        // foreach ($regras as $regra) {
        //     $estoque = Estoque::join('carros', 'estoques.carro_id', 'carros.id')
        //         ->whereRaw("$regra->coluna_carro LIKE '" . $interesse[$regra->coluna_interesse] . "'")->get();

        //     foreach ($estoque as $estoque) {
        //         $carrosencontrados[$estoque->id] = isset($carrosencontrados[$estoque->id]) ?
        //             $carrosencontrados[$estoque->id] + $regra->prioridade : $regra->prioridade;
        //     }
        // }

        // uasort($carrosencontrados, function ($a, $b) {
        //     return $b - $a;
        // });

        // return [
        //     'interesse' => $interesse,
        //     'probabilidade' => $carrosencontrados,
        //     'carros' => Estoque::with('carro')->whereIn('id', array_keys($carrosencontrados))->get()
        // ];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Interesse  $interesse
     * @return \Illuminate\Http\Response
     */
    public function edit(Interesse $interesse)
    {
        return $interesse;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Interesse  $interesse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Interesse $interesse)
    {
        return $interesse;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Interesse  $interesse
     * @return \Illuminate\Http\Response
     */
    public function destroy(Interesse $interesse)
    {
        if ($interesse->delete()) {
            return [
                'status' => 1
            ];
        }

        return [
            'status' => 0,
        ];
    }

    public function list()
    {
        return Interesse::all();
    }
}
