<?php

namespace App\Http\Controllers\Api;

use App\CaracteristicaEstoque;
use App\Estoque;
use App\Http\Controllers\Controller;
use App\Match;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class EstoqueController extends Controller
{
    private $with = 'join';

    public function index()
    {
        $qtd = request()->input('qtd', 100000);

        $with = request()->input($this->with);
        if ($with) {
            $relations = explode(",", $with);
            $dados = Estoque::with($relations)->orderByDesc('id')->paginate($qtd);
        } else {
            $dados = Estoque::orderByDesc('id')->paginate($qtd);
        }

        return $dados->items();
    }

    public function store(Request $request)
    {
        $request->validate([
            'caracteristicas' => 'array',
            'carro_id' => 'required',
            'observacoes' => '',
        ]);

        $resultados = [];
        try {
            DB::beginTransaction();
            DB::table('carros')->where('id', $request['carro_id'])->update(['uso' => DB::raw('uso + 1')]);

            $est = new Estoque([
                'carro_id' => $request['carro_id'],
                'observacoes' => $request['observacoes'],
            ]);
            $est->save();

            $ces = [];
            foreach ($request['caracteristicas'] as $caracteristica) {
                $ces[] = [
                    'caracteristica_id' => $caracteristica['id'],
                    'estoque_id' => $est->id,
                    'valor' => $caracteristica['valor'],
                ];
            }
            CaracteristicaEstoque::insert($ces);

            $est->load(['caracteristicas.descricao', 'carro.marca']);
            $resultados = Match::findInteresses($est, 10);

            DB::commit();
        } catch (Throwable $th) {
            return [
                'message' => $th->getMessage(),
                'request' => $request->toArray(),
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
            $estoque = Estoque::with($relations)->find($id);
        } else {
            $estoque = Estoque::find($id);
        }

        if (strpos($with, "caracteristicas") !== false) {
            foreach ($estoque->caracteristicas as $i => $_) {
                $estoque->caracteristicas[$i]->valor_opcao;
            }
        }

        return $estoque;
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'caracteristicas' => 'array',
            'carro_id' => 'required',
            'observacoes' => '',
        ]);

        $resultados = [];
        try {
            DB::beginTransaction();

            $est = Estoque::find($id);
            $est->update([
                'carro_id' => $request['carro_id'],
                'observacoes' => $request['observacoes'],
            ]);
            $est->save();

            $ces = [];
            foreach ($request['caracteristicas'] as $caracteristica) {
                $ces[] = [
                    'caracteristica_id' => $caracteristica['id'],
                    'estoque_id' => $est->id,
                    'valor' => $caracteristica['valor'],
                ];
            }
            CaracteristicaEstoque::where('estoque_id', $est->id)->delete();
            CaracteristicaEstoque::insert($ces);

            $est->load(['caracteristicas.descricao', 'carro.marca']);
            $resultados = Match::findInteresses($est, 5);

            DB::commit();
        } catch (Throwable $th) {
            return [
                'message' => $th->getMessage(),
                'request' => $request->toArray(),
            ];
        }

        return [
            'status' => 1,
            'resultados' => $resultados
        ];
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        CaracteristicaEstoque::where('estoque_id', $id)->delete();
        Match::where('estoque_id', $id)->delete();
        Estoque::where('id', $id)->delete();

        DB::commit();

        return [
            'status' => 1,
        ];
    }

    public function search()
    {
        $q = request()->input('q', false);

        if ($q) {
            $query = Estoque::whereHas('carro', function ($query) use ($q) {
                $query->whereRaw("
                    nome LIKE '%$q%'
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
        $est = Estoque::with(['caracteristicas.descricao', 'carro.marca'])->find($id);
        $matches = Match::findInteresses($est, 10);

        return $matches;
    }
}
