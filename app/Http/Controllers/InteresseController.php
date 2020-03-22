<?php

namespace App\Http\Controllers;

use App\Carro;
use App\Estoque;
use App\Interesse;
use App\Match;
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
        $qtd = request()->input('qtd');
        if ($qtd) {
            request()->session()->put('qtd', $qtd);
        } else {
            $qtd = request()->session()->get('qtd', 10);
        }

        $dados = Interesse::leftJoin('carros', 'interesses.carro_id', 'carros.id')
            ->leftJoin('clientes', 'clientes.id', 'interesses.cliente_id')
            ->selectRaw("clientes.nome as cliente, carros.nome as carro, interesses.*")
            ->orderByDesc('interesses.created_at')
            ->paginate($qtd);

        return view('pages.interesse.index', [
            'dados' => $dados,
        ]);
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
    public function show($id)
    {
        $interesse = Interesse::find($id);
        $matches = Match::findEstoques($interesse)->toArray();

        $interesse->marca = $interesse->carro->marca->nome;
        $interesse->carro = $interesse->carro->nome;

        return view('pages.padrao.verdados', [
            'dados' => [
                'interesse' => $interesse->getAttributes(),
                'estoques' => $matches,
            ],
            'highlight' => true,
        ]);
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
