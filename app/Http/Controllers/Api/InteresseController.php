<?php

namespace App\Http\Controllers\Api;

use App\Caracteristica;
use App\CaracteristicaInteresse;
use App\Interesse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InteresseController extends Controller
{
    private $with = 'join';

    public function index()
    {
        $qtd = request()->input('qtd', 100);

        // ignorar colunas
        $ignorar_string = request()->input('ignorar', "");
        $ignorar = [];
        foreach (explode(',', $ignorar_string) as $coluna) {
            if (trim($coluna) && array_search($coluna, $ignorar) == false) {
                $ignorar[] = $coluna;
            }
        }

        // filtros
        $filtros = [];
        foreach (request()->input() as $nome => $valor) {
            if ($nome != 'page' && $nome != 'ref' &&  $nome != 'ignorar' && $nome != 'qtd' && $nome != 'api_token' && $valor != "") {
                $filtros[$nome] = $valor;
            }
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

        return $dados->items();
    }

    public function store(Request $request)
    {
        $data = new Interesse($request->all());
        if ($data->save()) {
            return [
                'status' => 1,
                'data' => $data,
            ];
        }

        return [
            'status' => 0,
            'data' => null,
        ];
    }

    public function show(int $id)
    {
        if (request()->input($this->with)) {
            return Interesse::with(request()->input($this->with))->find($id);
        }

        return Interesse::find($id);
    }

    public function update(Request $request, int $id)
    {
        if (count($request->all())) {
            $data = Interesse::find($id);
            $data->update($request->all());
            return [
                'status' => 1,
                'data' => $data,
            ];
        }

        return [
            'status' => 0,
            'data' => null,
        ];
    }

    public function destroy(int $id)
    {
        return [
            'status' => 0,
        ];
    }
}
