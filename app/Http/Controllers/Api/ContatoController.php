<?php

namespace App\Http\Controllers\Api;

use App\Caracteristica;
use App\CaracteristicaInteresse;
use App\Carro;
use App\Cliente;
use App\Http\Controllers\Controller;
use App\Interesse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContatoController extends Controller
{
    public function index()
    {
        DB::beginTransaction();

        $nome = request()->input('nome');
        $telefone = request()->input('telefone');

        $cliente = Cliente::where('telefone', $telefone)->first();

        if (!$cliente) {
            $cliente = new Cliente([
                'nome' => $nome,
                'telefone' => $telefone
            ]);
            $cliente->save();
        }

        $interesse = new Interesse([
            'cliente_id' => $cliente->id,
            'carro_id' => null,
        ]);
        $interesse->save();

        $palavras = explode(" ", strtolower($nome));
        [$carros, $caracteristicas] = ContatoController::parse($palavras, $interesse);

        DB::rollback();

        return [
            'cliente' => $cliente,
            'caracteristicas' => $caracteristicas,
            'carros' => $carros
        ];
    }

    public function store($request)
    {
    }

    public static function parse($palavras, $interesse)
    {
        $carros = [];
        $caracteristicas = [];

        foreach ($palavras as $i => $palavra) {
            if ($palavra !== 'int') {
                $cars = Carro::where('nome', 'like', "%$palavra%")->select(['id'])->get();
                foreach ($cars as $car) {
                    $carros[] = $car->id;
                }
            }
        }

        $carros = Carro::whereIn('id', $carros)->get()->toArray();

        foreach ($palavras as $i => $palavra) {
            if ($palavra !== 'int') {
                $cars = Carro::where('nome', 'like', "%$palavra%")->get()->toArray();
                foreach ($cars as $car) {
                    $carros[] = $car;
                }

                if ($palavra === 'até' || $palavra === 'ate') { // valor até
                    if ($i + 1 < count($palavras)) {
                        $palavra_valor = $palavras[$i + 1];

                        $valor = intval($palavra_valor);

                        if ($valor > 0) {
                            $unidade = str_replace($valor, "", $palavra_valor);

                            if (strlen($unidade) < 1 && $i + 2 < count($palavras)) {
                                $unidade = $palavras[$i + 2];
                            }

                            switch ($unidade) {
                                case "k":
                                case "mil":
                                    $valor *= 1000;
                                    break;
                            }

                            if ($unidade) {
                                $caracteristica = Caracteristica::where('nome', 'valor')->first();
                                $caracteristica_int = new CaracteristicaInteresse([
                                    'caracteristica_id' => $caracteristica->id,
                                    'interesse_id' => $interesse->id,
                                    'valor' => $valor,
                                    'comparador' => "<",
                                ]);
                                $caracteristica_int->save();
                                $caracteristicas[] = $caracteristica_int;
                            }
                        }
                    }
                } elseif (is_numeric($palavra) && $i >= count($palavras) - 1 && ((strlen($palavra) == 4 && $palavra <= date('Y') && $palavra > 1900) || strlen($palavra) == 2)) { // ano
                    if (strlen($palavra) == 2) {
                        if ($palavra <= date('Y')) {
                            $palavra += 2000;
                        } else {
                            $palavra += 1900;
                        }
                    }

                    $caracteristica = Caracteristica::where('nome', 'ano')->first();
                    $caracteristica_int = new CaracteristicaInteresse([
                        'caracteristica_id' => $caracteristica->id,
                        'interesse_id' => $interesse->id,
                        'valor' => $palavra,
                        'comparador' => "~",
                    ]);
                    $caracteristica_int->save();
                    $caracteristicas[] = $caracteristica_int;
                }
            }
        }

        return [
            $carros,
            $caracteristicas
        ];
    }
}
