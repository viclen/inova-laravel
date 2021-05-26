<?php

namespace App\Http\Controllers\Api;

use App\Caracteristica;
use App\CaracteristicaCarroCliente;
use App\CaracteristicaInteresse;
use App\CarroCliente;
use App\Cliente;
use App\Interesse;
use App\Http\Controllers\Controller;
use App\Match;
use App\Regra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class InteresseController extends Controller
{
    private $with = 'join';

    public function index()
    {
        $qtd = request()->input('qtd', 100000);

        $with = request()->input($this->with);
        if ($with) {
            $relations = explode(",", $with);
            $dados = Interesse::with($relations)->orderByDesc('id')->paginate($qtd);
        } else {
            $dados = Interesse::orderByDesc('id')->paginate($qtd);
        }

        return $dados->items();
    }

    public function store(Request $request)
    {
        $request->validate([
            'interesses' => 'array',
            'cliente' => 'required',
            'troca' => '',
        ]);

        $resultados = [];

        try {
            $cliente = $request['cliente'];

            if (!isset($cliente['id']) || !$cliente['id']) {
                if (!isset($cliente['nome']) || !isset($cliente['telefone']) || !$cliente['nome'] || !$cliente['telefone']) {
                    return [
                        'status' => 0
                    ];
                }

                $cliente = Cliente::where("telefone", $cliente['telefone'])->first();

                if(!$cliente){
                    $cliente = new Cliente([
                        'nome' => $cliente['nome'],
                        'telefone' => $cliente['telefone'],
                        'endereco' => isset($cliente['endereco']) ? $cliente['endereco'] : '',
                        'cidade' => isset($cliente['cidade']) ? $cliente['cidade'] : '',
                        'email' => isset($cliente['email']) ? $cliente['email'] : '',
                        'cpf' => isset($cliente['cpf']) ? $cliente['cpf'] : ''
                    ]);
                }

                $cliente->nome = $cliente['nome'];
                $cliente->save();
            }

            $cliente = json_decode(json_encode($cliente), true);

            foreach ($request['interesses'] as $i => $interesse) {
                $int = new Interesse([
                    'cliente_id' => $cliente['id'],
                    'carro_id' => $interesse['carro_id'],
                    'observacoes' => $interesse['observacoes'],
                    'origem' => $interesse['origem'],
                ]);
                $int->save();

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

                $int->load(['caracteristicas.descricao', 'carro.marca']);
                $matches = $this->match($int->id);
                if (count($matches)) {
                    $matches[0]['interesse'] = $int;
                    $resultados[] = $matches[0];
                }

                DB::table('carros')->where('id', $interesse['carro_id'])->update(['uso' => DB::raw('uso + 1')]);
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

                DB::table('carros')->where('id', $troca['carro_id'])->update(['uso' => DB::raw('uso + 1')]);
            }
        } catch (Throwable $th) {
            return [
                'message' => $th->getMessage(),
                'request' => $request->toArray()
            ];
        }

        return [
            'status' => 1,
            'resultados' => $resultados
        ];
    }

    public function show(int $id)
    {
        $with = request()->input($this->with);
        if ($with) {
            $relations = explode(",", $with);
            $interesse = Interesse::with($relations)->find($id);
        } else {
            $interesse = Interesse::find($id);
        }

        if (strpos($with, "caracteristicas") !== false) {
            foreach ($interesse->caracteristicas as $i => $_) {
                $interesse->caracteristicas[$i]->valor_opcao;
            }
        }

        return $interesse;
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'caracteristicas' => 'array',
            'carro_id' => 'required',
            'observacoes' => '',
        ]);

        Interesse::where('id', $id)->update([
            'carro_id' => $request['carro_id'],
            'observacoes' => $request['observacoes'],
            'origem' => $request['origem'],
        ]);

        CaracteristicaInteresse::where('interesse_id', $id)->delete();

        $cis = [];
        foreach ($request['caracteristicas'] as $caracteristica) {
            $cis[] = [
                'caracteristica_id' => $caracteristica['id'],
                'interesse_id' => $id,
                'valor' => $caracteristica['valor']['valor'],
                'comparador' => $caracteristica['valor']['comparador'],
            ];
        }
        CaracteristicaInteresse::insert($cis);

        return [
            'status' => 1,
        ];
    }

    public function destroy(int $id)
    {
        CaracteristicaInteresse::where('interesse_id', $id)->delete();
        Match::where('interesse_id', $id)->delete();
        Interesse::where('id', $id)->delete();

        return [
            'status' => 1,
        ];
    }

    public function search()
    {
        $q = request()->input('q', false);

        if ($q) {
            $query = Interesse::whereHas('carro', function ($query) use ($q) {
                $query->whereRaw("
                    nome LIKE '%$q%'
                ");
            })->orWhereHas('cliente', function ($query) use ($q) {
                $query->whereRaw("
                    nome LIKE '%$q%' OR
                    cidade LIKE '%$q%'
                ");
            });

            $qtd = request()->input('qtd', 100000);

            $with = request()->input($this->with);
            if ($with) {
                $relations = explode(",", $with);
                $dados = $query->with($relations)->orderByDesc('id')->paginate($qtd);
            } else {
                $dados = $query->orderByDesc('id')->paginate($qtd);
            }

            return $dados->items();
        }
        return abort(422);
    }

    public function match(int $id)
    {
        $int = Interesse::with(['caracteristicas.descricao', 'carro.marca'])->find($id);
        $matches = Match::findEstoques($int, 10);

        foreach ($matches as $i => $match) {
            $matches[$i]['caracteristicas'] = Caracteristica::whereIn('id', json_decode($match['caracteristicas']))->select(['id', 'nome'])->get();
        }

        return $matches;
    }

    public function advancedSearch()
    {
        $q = request()->textoPesquisa;

        $caracteristicas = request()->caracteristicas;

        $interesses = [];

        $regra = Regra::where([
            ['grupo', 'valor'],
            ['nome', 'porcentagem']
        ])->first();
        $porcentagem = $regra ? $regra->valor / 100 : 0.2;

        foreach ($caracteristicas as $caracteristica) {
            $encontrados = CaracteristicaInteresse::where([
                ['caracteristica_id', $caracteristica['id']]
            ])->where(function ($query) use ($caracteristica, $porcentagem) {
                $query->where('valor', '=', $caracteristica['valor']);
                if ($caracteristica['tipo'] == 1 || $caracteristica['tipo'] == 2) {
                    $query->orWhere([
                        ['comparador', '<'],
                        ['valor', '<=', $caracteristica['valor']]
                    ])->orWhere([
                        ['comparador', '>'],
                        ['valor', '>=', $caracteristica['valor']]
                    ])->orWhere([
                        ['comparador', '~'],
                        ['valor', '>=', $caracteristica['valor'] - $caracteristica['valor'] * $porcentagem],
                        ['valor', '<=', $caracteristica['valor'] + $caracteristica['valor'] * $porcentagem],
                    ]);
                } elseif ($caracteristica['tipo'] == 0) {
                    $query->orWhereRaw("
                        comparador = '<'
                        AND '$caracteristica[valor]' LIKE CONCAT(`valor`, '%')
                    ")->orWhereRaw("
                        comparador = '>'
                        AND '$caracteristica[valor]' LIKE CONCAT('%', `valor`)
                    ")->orWhereRaw("
                        comparador = '~'
                        AND '$caracteristica[valor]' LIKE CONCAT('%', `valor`, '%')
                    ");
                }
            })
                ->selectRaw('DISTINCT interesse_id, caracteristica_id')
                ->get();

            foreach ($encontrados as $encontrado) {
                $interesses[] = $encontrado->interesse_id;
            }
        }

        if ($q) {
            $query = Interesse::whereHas('carro', function ($query) use ($q) {
                $query->whereRaw("
                    nome LIKE '%$q%'
                ");
            })->orWhereHas('cliente', function ($query) use ($q) {
                $query->whereRaw("
                    nome LIKE '%$q%' OR
                    cidade LIKE '%$q%'
                ");
            })->orWhereIn('id', $interesses);

            $qtd = request()->input('qtd', 100000);

            $with = 'carro.marca,cliente,caracteristicas.descricao';
            if ($with) {
                $relations = explode(",", $with);
                $dados = $query->with($relations)->orderByDesc('id')->paginate($qtd);
            } else {
                $dados = $query->orderByDesc('id')->paginate($qtd);
            }

            return $dados->items();
        }

        return request();
    }
}
