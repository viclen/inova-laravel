<?php

namespace App\Http\Controllers;

use App\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoriaController extends Controller
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

        return view('pages.categoria.index', [
            'dados' => Categoria::withCount('carros as carros')->paginate($qtd),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.categoria.create', [
            'tipos' => (new Categoria())->getTypes(),
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
        ]);

        if ($validator->fails()) {
            return [
                'status' => 0,
                'errors' => $validator->errors()
            ];
        } else {
            $categoria = new Categoria($request->all());

            if ($categoria->save()) {
                return [
                    'status' => 1,
                    'data' => $categoria,
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
     * @param  \App\Categoria  $categoria
     * @return \Illuminate\Http\Response
     */
    public function show(Categoria $categoria)
    {
        return view('pages.padrao.verdados', [
            'dados' => [
                'categoria' => $categoria->getAttributes(),
                'carros' => $categoria->carros->toArray(),
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Categoria  $categoria
     * @return \Illuminate\Http\Response
     */
    public function edit(Categoria $categoria)
    {
        return view('pages.categoria.edit', [
            'tipos' => (new Categoria())->getTypes(),
            'dados' => $categoria,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Categoria  $categoria
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Categoria $categoria)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required',
        ]);

        if ($validator->fails()) {
            return [
                'status' => 0,
                'errors' => $validator->errors()
            ];
        } else {
            if ($categoria->update($request->all())) {
                return [
                    'status' => 1,
                    'data' => $categoria,
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
     * @param  \App\Categoria  $categoria
     * @return \Illuminate\Http\Response
     */
    public function destroy(Categoria $categoria)
    {
        if (!count($categoria->carros) && $categoria->delete()) {
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
        return Categoria::all();
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
        $dados = Categoria::withCount('carros as carros')
            ->where("categorias.nome", "like", $search)
            ->orderBy('categorias.nome')
            ->paginate($qtd);

        return view('pages.categoria.index', [
            'dados' => $dados,
        ]);
    }
}
