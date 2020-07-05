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
            DB::beginTransaction();

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
                $matches = Match::findEstoques($int, 1);
                if (count($matches)) {
                    $matches[0]->interesse = $int;
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

            DB::commit();
        } catch (Throwable $th) {
            return [
                'message' => $th->getMessage(),
                'request' => $request->toArray(),
                'i' => $i
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

        DB::beginTransaction();
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

        DB::commit();

        return [
            'status' => 1,
        ];
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        CaracteristicaInteresse::where('interesse_id', $id)->delete();
        Match::where('interesse_id', $id)->delete();
        Interesse::where('id', $id)->delete();

        DB::commit();

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

        return $matches;
    }
}
