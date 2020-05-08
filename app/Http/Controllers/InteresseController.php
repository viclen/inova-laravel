<?php

namespace App\Http\Controllers;

use App\Caracteristica;
use App\CaracteristicaCarroCliente;
use App\CaracteristicaInteresse;
use App\Carro;
use App\CarroCliente;
use App\Cliente;
use App\Estoque;
use App\Formatter;
use App\Interesse;
use App\Marca;
use App\Match;
use App\Regra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

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

        // ignorar colunas
        $ignorar_string = request()->input('ignorar', request()->input('ref') === 'r' ? "" : request()->session()->get('ignorar'));
        $ignorar = [];
        foreach (explode(',', $ignorar_string) as $coluna) {
            if (trim($coluna) && array_search($coluna, $ignorar) == false) {
                $ignorar[] = $coluna;
            }
        }
        request()->session()->put('ignorar', join(",", $ignorar));

        // filtros
        $filtros = [];
        foreach (request()->input() as $nome => $valor) {
            if ($nome != 'page' && $nome != 'ref' &&  $nome != 'ignorar' && $nome != 'qtd' && $valor != "") {
                $filtros[$nome] = $valor;
            }
        }
        if (count($filtros)) {
            request()->session()->put('filtros', json_encode($filtros));
        } elseif (request()->input('ref', '') === 'f') {
            request()->session()->put('filtros', '');
        } elseif (request()->session()->get('filtros', "")) {
            $filtros = json_decode(request()->session()->get('filtros'), true);
        }

        foreach ($filtros as $nome => $valor) {
            if (array_search($nome, $ignorar) !== false) {
                unset($filtros[$nome]);
            }
        }

        $resultado = null;
        if (count($filtros)) {
            $resultado = CaracteristicaInteresse::join('caracteristicas', 'caracteristicas.id', 'caracteristica_id')
                ->where(function ($query) use ($filtros) {
                    foreach ($filtros as $nome => $valor) {
                        $query->orWhere([
                            ['nome', $nome],
                            ['valor', $valor],
                        ]);
                    }
                })
                ->selectRaw('interesse_id, count(*) as number')
                ->groupBy('interesse_id')
                ->get()
                ->toArray();

            if (isset($filtros['carro']) && $filtros['carro'] || isset($filtros['marca']) && $filtros['marca']) {
                $ct = (isset($filtros['carro']) && $filtros['carro'] ? 1 : 0) + (isset($filtros['marca']) && $filtros['marca'] ? 1 : 0);

                $ints = Interesse::join('carros', 'carros.id', 'carro_id')
                    ->join('marcas', 'marcas.id', 'carros.marca_id')
                    ->where(function ($query) use ($filtros) {
                        if (isset($filtros['carro']) && $filtros['carro']) {
                            $query->where('carros.nome', 'LIKE', "%$filtros[carro]%");
                        }
                        if (isset($filtros['marca']) && $filtros['marca']) {
                            $query->where('marcas.nome', 'LIKE', "%$filtros[marca]%");
                        }
                    })
                    ->select('interesses.id as interesse_id')
                    ->get()
                    ->toArray();

                foreach ($ints as $int) {
                    $encontrado = false;
                    foreach ($resultado as $key => $r) {
                        if ($r['interesse_id'] == $int['interesse_id']) {
                            $resultado[$key]['number'] += $ct;
                            $encontrado = true;
                        }
                    }
                    if (!$encontrado) {
                        $resultado[] = [
                            'interesse_id' => $int['interesse_id'],
                            'number' => $ct
                        ];
                    }
                }
            }
        }

        // select
        $dados = Interesse::with(['caracteristicas.descricao', 'carro.marca'])
            ->orderByDesc('interesses.created_at')
            ->where(function ($query) use ($resultado, $filtros) {
                if ($resultado !== null) {
                    $query->whereIn('id', array_map(function ($item) use ($filtros) {
                        if ($item['number'] == count($filtros)) {
                            return $item['interesse_id'];
                        }
                    }, $resultado));
                } else {
                    $query->where('id', '>', 0);
                }
            })
            ->paginate($qtd);

        // colunas
        $colunas = array_map(function ($coluna) use ($ignorar) {
            if (array_search($coluna, $ignorar) === false) {
                return is_array($coluna) ? $coluna['nome'] : $coluna;
            }
        }, array_merge([
            'carro',
            'marca'
        ], Caracteristica::whereNotIn('caracteristicas.nome', $ignorar)
            ->selectRaw('DISTINCT caracteristicas.nome')
            ->get()
            ->toArray()));

        // opcoes colunas
        $opcoes_caracteristicas = Caracteristica::with('opcoes')
            ->whereIn('tipo', [4, 3])
            ->whereIn('nome', $colunas)
            ->get()
            ->toArray();

        $opcoes_colunas = [];
        foreach ($opcoes_caracteristicas as $coluna) {
            if ($coluna['tipo'] == 4) {
                $opcoes_colunas[$coluna['nome']] = [
                    [
                        'ordem' => 0,
                        'valor' => 'Não',
                    ],
                    [
                        'ordem' => 1,
                        'valor' => 'Sim',
                    ],
                ];
            } else {
                $opcoes_colunas[$coluna['nome']] = $coluna['opcoes'];
            }
        }

        return view('pages.interesse.index', [
            'interesses' => $dados->items(),
            'relacionamentos' => ['carro.marca', 'caracteristicas'],
            'ignorar' => $ignorar,
            'dados' => $dados,
            'colunas' => $colunas,
            'opcoes_colunas' => $opcoes_colunas,
            'filtros' => $filtros,
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
            'caracteristicas' => Caracteristica::with('opcoes')->get(),
            'marcas' => Marca::all(),
            'carros' => Carro::with('marca')->get(),
            'clientes' => Cliente::all(),
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
        $request->validate([
            'interesses' => 'array',
            'cliente' => 'required',
            'troca' => '',
        ]);

        try {
            $cliente = $request['cliente'];

            if (!isset($cliente['id']) || !$cliente['id']) {
                if (!isset($cliente['nome']) || !isset($cliente['telefone']) || !$cliente['nome'] || !$cliente['telefone']) {
                    return [
                        'status' => 0
                    ];
                }

                $cliente = new Cliente([
                    'nome' => $cliente['nome'],
                    'telefone' => $cliente['telefone'],
                    'endereco' => isset($cliente['endereco']) ? $cliente['endereco'] : '',
                    'cidade' => isset($cliente['cidade']) ? $cliente['cidade'] : '',
                    'email' => isset($cliente['email']) ? $cliente['email'] : '',
                    'cpf' => isset($cliente['cpf']) ? $cliente['cpf'] : ''
                ]);

                $cliente->save();
            }
            $cliente = json_decode(json_encode($cliente), true);

            $id = null;

            foreach ($request['interesses'] as $interesse) {
                $int = new Interesse([
                    'cliente_id' => $cliente['id'],
                    'carro_id' => $interesse['carro_id'],
                    'observacoes' => $interesse['observacoes'],
                    'origem' => $interesse['origem'],
                ]);
                $int->save();

                if (!$id) {
                    $id = $int->id;
                }

                $cis = [];
                foreach ($interesse['caracteristicas'] as $caracteristica) {
                    $cis[] = [
                        'caracteristica_id' => $caracteristica['id'],
                        'interesse_id' => $int->id,
                        'valor' => $caracteristica['valor']['valor'],
                        'comparador' => $caracteristica['valor']['comparador'],
                    ];
                }
                CaracteristicaInteresse::insert($cis);
            }

            if ($request['troca']) {
                $troca = $request['troca'];
                $carro_cliente_id = CarroCliente::insertGetId([
                    'cliente_id' => $cliente['id'],
                    'carro_id' => $troca['carro_id'],
                ]);

                $cccs = [];
                foreach ($troca['caracteristicas'] as $caracteristica) {
                    $cccs[] = [
                        'caracteristica_id' => $caracteristica['id'],
                        'carro_cliente_id' => $carro_cliente_id,
                        'valor' => $caracteristica['valor'],
                    ];
                }
                CaracteristicaCarroCliente::insert($cccs);
            }
        } catch (Throwable $th) {
            return [$th->getMessage()];
        }

        return [
            'status' => 1,
            'id' => $id,
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
        $interesse = Interesse::with(['caracteristicas.descricao', 'caracteristicas', 'carro.marca'])->find($id);

        $matches = Match::findEstoques($interesse);

        $ignorar = explode(',', request()->input('ignorar'));

        return view('pages.interesse.show', [
            'interesse' => $interesse,
            'matches' => $matches,
            'highlight' => true,
            'relacionamentos' => ['carro.marca', 'caracteristicas'],
            'ignorar' => $ignorar,
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
