<?php

namespace App\Http\Controllers;

use App\Carro;
use App\Cliente;
use App\Estoque;
use App\Formatter;
use App\Interesse;
use App\Match;
use App\Regra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        return view('pages.interesse.create', [
            'tipos' => (new Interesse())->getTypes(),
            'opcoes' => [
                'carros' => Carro::select(["id", "nome"])->get(),
                'clientes' => Cliente::select(["id", "nome"])->get(),
            ],
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
            'carro_id' => "required",
            'cliente_id' => "required",
            'valor' => "",
            'ano' => "",
            'cor' => "",
            'observacoes' => "",
            'financiado' => "",
            'origem' => "",
        ]);

        if ($validator->fails()) {
            return [
                'status' => 0,
                'errors' => $validator->errors()
            ];
        } else {
            $interesse = new Interesse($request->all());

            if ($interesse->save()) {
                return [
                    'status' => 1,
                    'data' => $interesse,
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
     * @param  \App\Interesse  $interesse
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $interesse = Interesse::find($id);
        $matches = Match::findEstoques($interesse)->toArray();

        if ($interesse->carro->categoria) {
            $interesse->categoria = $interesse->carro->categoria->nome;
        } else {
            unset($interesse->categoria);
        }

        if ($interesse->carro->marca) {
            $interesse->marca = $interesse->carro->marca->nome;
        } else {
            unset($interesse->marca);
        }

        $interesse->carro = $interesse->carro->nome;
        $interesse->valor = Formatter::valor($interesse->valor);

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
    public function edit($id)
    {
        $interesse = Interesse::find($id);

        return view('pages.interesse.edit', [
            'tipos' => (new Interesse())->getTypes(),
            'opcoes' => [
                'carros' => Carro::select(["id", "nome"])->get(),
                'clientes' => Cliente::select(["id", "nome"])->get(),
            ],
            'dados' => $interesse,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Interesse  $interesse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'carro_id' => "required",
            'cliente_id' => "required",
            'valor' => "",
            'ano' => "",
            'cor' => "",
            'observacoes' => "",
            'financiado' => "",
            'origem' => "",
        ]);

        if ($validator->fails()) {
            return [
                'status' => 0,
                'errors' => $validator->errors()
            ];
        } else {
            $interesse = Interesse::find($id);
            if ($interesse->update($request->all())) {
                return [
                    'status' => 1,
                    'data' => $interesse,
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
