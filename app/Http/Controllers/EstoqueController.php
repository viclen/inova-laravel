<?php

namespace App\Http\Controllers;

use App\Carro;
use App\Estoque;
use App\Match;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EstoqueController extends Controller
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

        $estoques = Estoque::join('carros', 'carros.id', 'carro_id')
            ->leftJoin('marcas', 'carros.marca_id', 'marcas.id')
            ->selectRaw('estoques.id, carros.nome as carro, marcas.nome as marca, estoques.valor, estoques.fipe, estoques.ano, estoques.placa, estoques.cor')
            ->paginate($qtd);

        return view('pages.estoque.index', [
            'dados' => $estoques,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.estoque.create', [
            'tipos' => (new Estoque())->getTypes(),
            'opcoes' => [
                'carros' => Carro::select(["id", "nome"])->get(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'valor' => "required",
            'carro_id' => "required",
            'fipe' => "",
            'placa' => "",
            'ano' => "",
            'cor' => "",
            'chassi' => "",
            'observacoes' => "",
        ]);

        if ($validator->fails()) {
            return [
                'status' => 0,
                'errors' => $validator->errors()
            ];
        } else {
            $estoque = new Estoque($request->all());

            if ($estoque->save()) {
                return [
                    'status' => 1,
                    'data' => $estoque,
                ];
            }
        }

        return [
            'status' => 0,
            'errors' => []
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Estoque  $estoque
     * @return \Illuminate\Http\Response
     */
    public function show(Estoque $estoque)
    {
        $matches = Match::findInteresses($estoque)->toArray();

        $estoque->marca = $estoque->carro->marca->nome;
        $estoque->carro = $estoque->carro->nome;

        return view('pages.padrao.verdados', [
            'dados' => [
                'estoque' => $estoque->getAttributes(),
                'interesses' => $matches,
            ],
            'highlight' => true,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Estoque  $estoque
     * @return \Illuminate\Http\Response
     */
    public function edit(Estoque $estoque)
    {
        return view('pages.estoque.edit', [
            'tipos' => (new Estoque())->getTypes(),
            'opcoes' => [
                'carros' => Carro::select(["id", "nome"])->get(),
            ],
            'dados' => $estoque,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Estoque  $estoque
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Estoque $estoque)
    {
        $validator = Validator::make($request->all(), [
            'valor' => "required",
            'carro_id' => "required",
            'fipe' => "",
            'placa' => "",
            'ano' => "",
            'cor' => "",
            'chassi' => "",
            'observacoes' => "",
        ]);

        if ($validator->fails()) {
            return [
                'status' => 0,
                'errors' => $validator->errors()
            ];
        } else {
            if ($estoque->update($request->all())) {
                return [
                    'status' => 1,
                    'data' => $estoque,
                ];
            }
        }

        return [
            'status' => 0,
            'errors' => []
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Estoque  $estoque
     * @return \Illuminate\Http\Response
     */
    public function destroy(Estoque $estoque)
    {
        if ($estoque->delete()) {
            return [
                'status' => 1
            ];
        }

        return [
            'status' => 0
        ];
    }

    public function search($search)
    {
        $qtd = request()->input('qtd');
        if ($qtd) {
            request()->session()->put('qtd', $qtd);
        } else {
            $qtd = request()->session()->get('qtd', 10);
        }

        $search = "%" . addslashes($search) . "%";
        $estoques = Estoque::leftJoin('carros', 'carros.id', 'carro_id')
            ->leftJoin('marcas', 'carros.marca_id', 'marcas.id')
            ->where("carros.nome", "like", $search)
            ->orWhere("marcas.nome", "like", $search)
            ->orWhere("estoques.ano", "like", $search)
            ->orWhere("estoques.placa", "like", $search)
            ->orWhere("estoques.cor", "like", $search)
            ->selectRaw('estoques.id, carros.nome as carro, marcas.nome as marca, estoques.valor, estoques.fipe, estoques.ano, estoques.cor')
            ->paginate($qtd);

        return view('pages.estoque.index', [
            'dados' => $estoques,
        ]);
    }
}
