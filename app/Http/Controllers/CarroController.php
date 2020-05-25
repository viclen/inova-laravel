<?php

namespace App\Http\Controllers;

use App\Carro;
use App\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CarroController extends Controller
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

        $dados = Carro::leftJoin('estoques', 'estoques.carro_id', 'carros.id')
            ->leftJoin('marcas', 'marcas.id', 'carros.marca_id')
            ->selectRaw("carros.*, marcas.nome as marca, count(estoques.id) as estoque")
            ->groupBy('carros.id')
            ->orderByDesc('estoque')
            ->orderBy('carros.nome')
            ->paginate($qtd);

        return view('pages.carro.index', [
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
        return view('pages.carro.create', [
            'tipos' => (new Carro)->getTypes(),
            'opcoes' => [
                'marcas' => Marca::select(["id", "nome"])->get(),
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
            'nome' => 'required',
            'marca_id' => 'required',
        ]);

        if ($validator->fails()) {
            return [
                'status' => 0,
                'errors' => $validator->errors()
            ];
        } else {
            $carro = new Carro($request->all());

            if ($carro->save()) {
                return [
                    'status' => 1,
                    'data' => $carro,
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
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $carro = Carro::find($id);

        $carro->marca = $carro->marca->nome;

        $estoques = [];
        foreach ($carro->estoques as $estoque) {
            $estoques[] = array_merge($estoque->dadosTabela(), ['id' => $estoque->id]);
        }

        $interesses = [];
        foreach ($carro->interesses as $interesse) {
            $interesses[] = array_merge($interesse->dadosTabela(), ['id' => $interesse->id]);
        }

        return view('pages.carro.show', [
            'dados' => [
                'carro' => $carro->getAttributes(),
                'estoques' => $estoques,
                'interesses' => $interesses,
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function edit(Carro $carro)
    {
        return view('pages.carro.edit', [
            'tipos' => (new Carro)->getTypes(),
            'opcoes' => [
                'marcas' => Marca::select(["id", "nome"])->get(),
            ],
            'dados' => $carro,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Carro $carro)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required',
            'marca_id' => 'required',
        ]);

        if ($validator->fails()) {
            return [
                'status' => 0,
                'errors' => $validator->errors()
            ];
        } else {
            $carro->nome = $request['nome'];
            $carro->marca_id = $request['marca_id'];

            if ($carro->save()) {
                return [
                    'status' => 1,
                    'data' => $carro,
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
     * @param  \App\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function destroy(Carro $carro)
    {
        if (!count($carro->estoques) && !count($carro->interesses) && $carro->delete()) {
            return [
                'status' => 1
            ];
        }

        return [
            'status' => 0
        ];
    }

    public function list()
    {
        return Carro::all();
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
        $dados = DB::table('carros')
            ->leftJoin('estoques', 'estoques.carro_id', 'carros.id')
            ->leftJoin('marcas', 'marcas.id', 'carros.marca_id')
            ->where("carros.nome", "like", $search)
            ->orWhere("marcas.nome", "like", $search)
            ->selectRaw("carros.*, marcas.nome as marca, count(estoques.id) as estoque")
            ->groupBy('carros.id')
            ->orderByDesc('estoque')
            ->paginate($qtd);

        return view('pages.carro.index', [
            'dados' => $dados,
        ]);
    }
}
