<?php

namespace App\Http\Controllers;

use App\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
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

        return view('pages.cliente.index', [
            'dados' => Cliente::select(['id', 'nome', 'telefone', 'email', 'cidade'])->paginate($qtd),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.cliente.create', [
            'tipos' => (new Cliente)->getTypes(),
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
            'telefone' => 'required|regex:/^\([1-9]{2}\) [0-9]{4,5}\-[0-9]{4}$/',
            'endereco' => '',
            'cidade' => '',
            'email' => '',
            'cpf' => '',
        ]);

        if ($validator->fails()) {
            return [
                'status' => 0,
                'errors' => $validator->errors()
            ];
        } else {
            $cliente = new Cliente($request->all());

            if ($cliente->save()) {
                return [
                    'status' => 1,
                    'data' => $cliente,
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
     * @param  \App\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function show(Cliente $cliente)
    {
        $carros = $cliente->carros->toArray();
        // $this->delete_col($carros, 'pivot');
        $cliente->load('interesses.caracteristicas.descricao');

        $ignorar = explode(',', request()->input('ignorar'));

        $interesses = $cliente->interesses;
        $caracteristicas = [];
        foreach ($interesses as $interesse) {
            foreach ($interesse->caracteristicas as $caracteristica) {
                if (!isset($caracteristicas[$caracteristica->caracteristica_id]) && array_search($caracteristica->descricao->nome, $ignorar) === false) {
                    $caracteristicas[$caracteristica->caracteristica_id] = $caracteristica->descricao->nome;
                }
            }
        }

        return view('pages.cliente.show', [
            'dados' => [
                'cliente' => $cliente->getAttributes(),
                'carros' => $carros,
            ],
            'caracteristicas' => $caracteristicas,
            'interesses' => $interesses,
            'ignorar' => $ignorar,
        ]);
    }

    public function delete_col(&$array, $key)
    {
        return array_walk($array, function (&$v) use ($key) {
            unset($v[$key]);
        });
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function edit(Cliente $cliente)
    {
        return view('pages.cliente.edit', [
            'tipos' => (new Cliente)->getTypes(),
            'dados' => $cliente,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cliente $cliente)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required',
            'telefone' => 'required|regex:/^\([1-9]{2}\) [0-9]{4,5}\-[0-9]{4}$/',
            'endereco' => '',
            'cidade' => '',
            'email' => '',
            'cpf' => '',
        ]);

        if ($validator->fails()) {
            return [
                'status' => 0,
                'errors' => $validator->errors()
            ];
        } else {
            if ($cliente->update($request->all())) {
                return [
                    'status' => 1,
                    'data' => $cliente,
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
     * @param  \App\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cliente $cliente)
    {
        if (count($cliente->carros) || count($cliente->interesses)) {
            return [
                'status' => 0
            ];
        } elseif ($cliente->delete()) {
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
        return Cliente::all();
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
        $dados = DB::table('clientes')
            ->where("clientes.nome", "like", $search)
            ->orWhere("clientes.telefone", "like", $search)
            ->orWhere("clientes.cidade", "like", $search)
            ->orWhere("clientes.email", "like", $search)
            ->orWhere("clientes.cpf", "like", $search)
            ->selectRaw('
                clientes.id,
                clientes.nome,
                clientes.email,
                clientes.telefone,
                clientes.cidade,
                clientes.cpf
            ')
            ->orderBy('clientes.nome')
            ->paginate($qtd);

        return view('pages.cliente.index', [
            'dados' => $dados,
        ]);
    }
}
