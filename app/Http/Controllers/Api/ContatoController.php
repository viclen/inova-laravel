<?php

namespace App\Http\Controllers\Api;

use App\Caracteristica;
use App\CaracteristicaInteresse;
use App\Carro;
use App\Cliente;
use App\Http\Controllers\Controller;
use App\Interesse;
use App\OpcaoCaracteristica;
use Exception;
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

        $ignorar = [
            "INT",
            "MODELO"
        ];

        $palavras = array_slice(explode(" ", str_replace($ignorar, "", strtoupper($nome))), 1);

        try {
            [$carros, $caracteristicas, $nao_encontradas, $usadas] = ContatoController::parse($palavras, $interesse);
        } catch (\Throwable $th) {
            return [
                'error' => $th->getMessage()
            ];
        }

        DB::rollback();

        return [
            'cliente' => $cliente,
            'caracteristicas' => $caracteristicas,
            'carros' => $carros,
            'nao_encontradas' => $nao_encontradas,
            'usadas' => $usadas
        ];
    }

    public function store($request)
    {
    }

    public static function parse($palavras, $interesse)
    {
        foreach ($palavras as $i => $palavra) {
            if (!$palavra) {
                unset($palavras[$i]);
            }
        }

        $carros = [];
        $caracteristicas = [];

        $caracteristicas_usadas = [];

        $usadas = [];

        foreach ($palavras as $i => $palavra) {
            if ($palavra == 'ATÉ' || $palavra == 'ATE' || strtolower($palavra) == 'até' || strtolower($palavra) == 'ate') { // valor até
                if ($i + 1 < count($palavras)) {
                    $palavra_valor = $palavras[$i + 1];

                    $valor = intval($palavra_valor);

                    if ($valor > 0) {
                        $usadas[] = $palavras[$i + 1];

                        $unidade = str_replace($valor, "", $palavra_valor);

                        if (strlen($unidade) < 1 && $i + 2 < count($palavras)) {
                            $unidade = $palavras[$i + 2];
                            if ($unidade) {
                                $usadas[] = $palavras[$i + 2];
                            }
                        }

                        switch ($unidade) {
                            case "K":
                            case "MIL":
                                $valor *= 1000;
                                break;
                            case "V":
                                $unidade = "";
                                break;
                        }

                        if ($unidade) {
                            $caracteristica = Caracteristica::where('nome', 'valor')->first();

                            if (array_search($caracteristica->id, $caracteristicas_usadas) == false) {
                                $caracteristicas_usadas[] = $caracteristica->id;
                                $caracteristica_int = new CaracteristicaInteresse([
                                    'caracteristica_id' => $caracteristica->id,
                                    'interesse_id' => $interesse->id,
                                    'valor' => $valor,
                                    'comparador' => "<",
                                ]);
                                $caracteristica_int->caracteristica = $caracteristica;
                                $caracteristicas[] = $caracteristica_int;
                            }

                            $usadas[] = $palavra;
                        }
                    }
                }
            } elseif (is_numeric($palavra) && $i >= count($palavras) - 1 && ((strlen($palavra) == 4 && $palavra <= date('Y') && $palavra > 1900) || strlen($palavra) == 2)) { // ano
                if (strlen($palavra) == 2) {
                    if ($palavra <= date('Y')) {
                        $valor = $palavra + 2000;
                    } else {
                        $valor = $palavra + 1900;
                    }
                }

                $caracteristica = Caracteristica::where('nome', 'ano')->first();
                if (array_search($caracteristica->id, $caracteristicas_usadas) == false) {
                    $caracteristicas_usadas[] = $caracteristica->id;
                    $caracteristica_int = new CaracteristicaInteresse([
                        'caracteristica_id' => $caracteristica->id,
                        'interesse_id' => $interesse->id,
                        'valor' => $valor,
                        'comparador' => "~",
                    ]);
                    $caracteristica_int->caracteristica = $caracteristica;
                    $caracteristicas[] = $caracteristica_int;
                    $usadas[] = $palavra;
                }
            } elseif ($palavra == "NOVO") {
                $caracteristica = Caracteristica::where('nome', 'km')->first();
                if (array_search($caracteristica->id, $caracteristicas_usadas) == false) {
                    $caracteristicas_usadas[] = $caracteristica->id;
                    $caracteristica_int = new CaracteristicaInteresse([
                        'caracteristica_id' => $caracteristica->id,
                        'interesse_id' => $interesse->id,
                        'valor' => 0,
                        'comparador' => "=",
                    ]);
                    $caracteristica_int->caracteristica = $caracteristica;
                    $caracteristicas[] = $caracteristica_int;
                    $usadas[] = $palavra;
                }
            } elseif ($palavra == "FINANCIADO" || $palavra == "100%") {
                $caracteristica = Caracteristica::where('nome', 'financiado')->first();
                if (array_search($caracteristica->id, $caracteristicas_usadas) == false) {
                    $caracteristicas_usadas[] = $caracteristica->id;
                    $caracteristica_int = new CaracteristicaInteresse([
                        'caracteristica_id' => $caracteristica->id,
                        'interesse_id' => $interesse->id,
                        'valor' => 1,
                        'comparador' => "=",
                    ]);
                    $caracteristica_int->caracteristica = $caracteristica;
                    $caracteristicas[] = $caracteristica_int;
                    $usadas[] = $palavra;
                }
            } elseif ($opcao = OpcaoCaracteristica::where('valor', $palavra)->first()) {
                $caracteristica = Caracteristica::find($opcao->caracteristica_id);
                if (array_search($caracteristica->id, $caracteristicas_usadas) == false) {
                    $caracteristicas_usadas[] = $caracteristica->id;
                    $caracteristica_int = new CaracteristicaInteresse([
                        'caracteristica_id' => $caracteristica->id,
                        'interesse_id' => $interesse->id,
                        'valor' => $opcao->ordem,
                        'comparador' => "=",
                    ]);
                    $caracteristica_int->caracteristica = $caracteristica;
                    $caracteristicas[] = $caracteristica_int;
                    $usadas[] = $palavra;
                }
            } elseif (intval($palavra) > 0 && array_search($palavra, $usadas) === false) {
                $valor = intval($palavra);
                $unidade = str_replace($valor, "", $palavra);

                if (strlen($unidade) < 1 && $i + 1 < count($palavras)) {
                    $unidade = $palavras[$i + 1];
                }

                switch ($unidade) {
                    case "K":
                    case "MIL":
                        $valor *= 1000;
                        break;
                    case "V":
                        $unidade = "";
                        break;
                }

                if ($unidade) {
                    $caracteristica = Caracteristica::where('nome', 'valor')->first();
                    if (array_search($caracteristica->id, $caracteristicas_usadas) == false) {
                        $caracteristicas_usadas[] = $caracteristica->id;
                        $caracteristica_int = new CaracteristicaInteresse([
                            'caracteristica_id' => $caracteristica->id,
                            'interesse_id' => $interesse->id,
                            'valor' => $valor,
                            'comparador' => "~",
                        ]);
                        $caracteristica_int->caracteristica = $caracteristica;
                        $caracteristicas[] = $caracteristica_int;
                        $usadas[] = $palavra;
                    }
                }
            }
        }

        if (count($palavras) > 1) {
            $cars = Carro::where(array_map(function ($palavra) use ($usadas) {
                if (array_search($palavra, $usadas) === false) {
                    return ['nome', 'REGEXP', "[[:<:]]" . $palavra . "[[:>:]]"];
                } else {
                    return ['id', '>', 0];
                }
            }, $palavras))->select(['id'])->get();
            if (count($cars)) {
                $usadas = $palavras;

                foreach ($cars as $car) {
                    $carros[] = $car->id;
                }
            }
        }

        if (!count($carros)) {
            foreach ($palavras as $i => $palavra) {
                if (array_search($palavra, $usadas) === false) {
                    if ($i + 1 < count($palavras)) {
                        $pesquisa = $palavra . " " . $palavras[$i + 1];

                        $cars = Carro::where('nome', 'like', "%$pesquisa%")->select(['id'])->get();
                        if (count($cars)) {
                            $usadas[] = $palavra;
                            $usadas[] = $palavras[$i + 1];

                            foreach ($cars as $car) {
                                $carros[] = $car->id;
                            }
                        }
                    }
                }
            }
        }

        if (!count($carros)) {
            foreach ($palavras as $i => $palavra) {
                if (array_search($palavra, $usadas) === false) {
                    $cars = Carro::where('nome', 'REGEXP', "[[:<:]]" . $palavra . "[[:>:]]")->select(['id'])->get();
                    if (count($cars)) {
                        $usadas[] = $palavra;
                    } else {
                        $cars = Carro::where('nome', 'like', "%$palavra%")->select(['id'])->get();
                        if (count($cars)) {
                            $usadas[] = $palavra;
                        }
                    }

                    foreach ($cars as $car) {
                        $carros[] = $car->id;
                    }
                }
            }
        }

        $carros = Carro::whereIn('id', $carros)->orderByRaw('LENGTH(nome)')->orderBy('nome')->get()->toArray();

        $nao_encontradas = [];
        foreach ($palavras as $i => $palavra) {
            if (array_search($palavra, $usadas) === false) {
                $nao_encontradas[] = $palavra;
            }
        }

        return [
            $carros,
            $caracteristicas,
            $nao_encontradas,
            $usadas
        ];
    }
}
