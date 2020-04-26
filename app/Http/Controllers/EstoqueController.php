<?php

namespace App\Http\Controllers;

use App\Caracteristica;
use App\CaracteristicaEstoque;
use App\Carro;
use App\Estoque;
use App\Formatter;
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
            $resultado = CaracteristicaEstoque::join('caracteristicas', 'caracteristicas.id', 'caracteristica_id')
                ->where(function ($query) use ($filtros) {
                    foreach ($filtros as $nome => $valor) {
                        $query->orWhere([
                            ['nome', $nome],
                            ['valor', $valor],
                        ]);
                    }
                })
                ->selectRaw('estoque_id, count(*) as number')
                ->groupBy('estoque_id')
                ->get()
                ->toArray();

            if (isset($filtros['carro']) && $filtros['carro'] || isset($filtros['marca']) && $filtros['marca']) {
                $ct = (isset($filtros['carro']) && $filtros['carro'] ? 1 : 0) + (isset($filtros['marca']) && $filtros['marca'] ? 1 : 0);

                $ints = Estoque::join('carros', 'carros.id', 'carro_id')
                    ->join('marcas', 'marcas.id', 'carros.marca_id')
                    ->where(function ($query) use ($filtros) {
                        if (isset($filtros['carro']) && $filtros['carro']) {
                            $query->where('carros.nome', 'LIKE', "%$filtros[carro]%");
                        }
                        if (isset($filtros['marca']) && $filtros['marca']) {
                            $query->where('marcas.nome', 'LIKE', "%$filtros[marca]%");
                        }
                    })
                    ->select('estoques.id as estoque_id')
                    ->get()
                    ->toArray();

                foreach ($ints as $int) {
                    $encontrado = false;
                    foreach ($resultado as $key => $r) {
                        if ($r['estoque_id'] == $int['estoque_id']) {
                            $resultado[$key]['number'] += $ct;
                            $encontrado = true;
                        }
                    }
                    if (!$encontrado) {
                        $resultado[] = [
                            'estoque_id' => $int['estoque_id'],
                            'number' => $ct
                        ];
                    }
                }
            }
        }

        // select
        $dados = Estoque::with(['caracteristicas.descricao', 'carro.marca'])
            ->orderByDesc('estoques.created_at')
            ->where(function ($query) use ($resultado, $filtros) {
                if ($resultado !== null) {
                    $query->whereIn('id', array_map(function ($item) use ($filtros) {
                        if ($item['number'] == count($filtros)) {
                            return $item['estoque_id'];
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

        return view('pages.estoque.index', [
            'estoques' => $dados->items(),
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
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $estoque = Estoque::with(['caracteristicas.descricao', 'caracteristicas', 'carro.marca'])->find($id);

        foreach ($estoque->caracteristicas as $i => $_) {
            $estoque->caracteristicas[$i]->valor_opcao;
        }

        $matches = Match::findInteresses($estoque);

        $ignorar = explode(',', request()->input('ignorar'));

        return view('pages.estoque.show', [
            'estoque' => $estoque,
            'matches' => $matches,
            'highlight' => true,
            'relacionamentos' => ['carro.marca', 'caracteristicas'],
            'ignorar' => $ignorar,
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
