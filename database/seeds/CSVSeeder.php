<?php

use App\Caracteristica;
use App\CaracteristicaCarroCliente;
use App\CaracteristicaInteresse;
use App\Carro;
use App\CarroCliente;
use App\Cliente;
use App\Interesse;
use App\OpcaoCaracteristica;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

const ALLOWED = "qwertyuiopasdfghjklzxcvbnm123456789-. ";
const COLUNAS = [
    'data' => 0,
    'origem' => 1,
    'carro' => 2,
    'troca' => 3,
    'nome' => 4,
    'telefone' => 5,
    'proposta' => 6,
];

function clearString($entrada = "")
{
    $saida = "";
    for ($i = 0; $i < strlen($entrada); $i++) {
        $letter = $entrada[$i];
        if (strpos(ALLOWED, strtolower($letter)) !== false) {
            $saida .= $letter;
        }
    }

    return trim($saida);
}

const NUMBERS = "1234567890";
const PATTERN = ["(99) 9999-9999", "(99) 99999-9999"];
function formatTelefone($entrada)
{
    $numeros = "";
    for ($i = 0; $i < strlen($entrada); $i++) {
        $letter = $entrada[$i];
        if (is_numeric($letter)) {
            $numeros .= $letter;
        }
    }

    $saida = "";
    $j = 0;
    $pattern = PATTERN[$j];
    $done = false;
    while (!$done) {
        $done = true;
        $k = 0;
        for ($i = 0; $i <= strlen($pattern); $i++) {
            if ($k < strlen($numeros)) {
                if ($i >= strlen($pattern)) {
                    if ($j < count(PATTERN) - 1) {
                        $done = false;
                        $pattern = PATTERN[++$j];
                        $saida = "";
                        break;
                    }
                } else {
                    if ($pattern[$i] == "9") {
                        $saida .= $numeros[$k++];
                    } else {
                        $saida .= $pattern[$i];
                    }
                }
            }
        }
    }

    return $saida;
}

class CSVSeeder extends Seeder
{
    public function run()
    {
        DB::unprepared('SET FOREIGN_KEY_CHECKS=0;');
        Cliente::truncate();
        Interesse::truncate();
        CaracteristicaInteresse::truncate();
        CaracteristicaCarroCliente::truncate();
        CarroCliente::truncate();

        $tabela = $this->readCSV('dados.csv', 1);

        foreach ($tabela as $i => $linha) {
            $nome = clearString($linha[COLUNAS['nome']]);

            if (strlen($nome) < 4) {
                continue;
            }

            $telefone = $linha[COLUNAS['telefone']];
            $carro = $linha[COLUNAS['carro']];

            $cliente = Cliente::where('telefone', $telefone)->first();

            if (!$cliente) {
                $cliente = Cliente::create([
                    'nome' => $nome,
                    'telefone' => $telefone
                ]);
            }

            $interesse = new Interesse([
                'cliente_id' => $cliente->id,
                'carro_id' => null,
            ]);

            $palavras = explode(" ", clearString(strtoupper($carro)));

            [$carros, $caracteristicas, $nao_encontradas, $usadas] = $this->parse($palavras, $interesse);

            if (count($carros) > 0) {
                $carro = $carros[0];
                $interesse->carro_id = $carro['id'];
                $interesse->save();
                echo $i + 2 . ": $cliente->nome -> carro: $carro[nome], caracteristicas:" . count($caracteristicas) . "\n";
            }
        }
        DB::unprepared('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function readCSV($filename, $header = 1)
    {
        $data = str_getcsv(Storage::get($filename), "\n"); //parse the rows
        foreach ($data as &$row) {
            $row = str_getcsv($row, ",");
        }

        return array_slice($data, $header);
    }

    public function parse($palavras, $interesse)
    {
        foreach ($palavras as $i => $palavra) {
            if (!$palavra || strlen($palavra) < 4) {
                array_splice($palavras, $i, 1);
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
                $valor = $palavra;

                if (strlen($palavra) == 2) {
                    if ($valor + 2000 <= date('Y')) {
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
            } elseif ($opcao = OpcaoCaracteristica::where('valor', 'like', "$palavra%")->first()) {
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
                        $cars = Carro::where('nome', 'like', "$palavra%")->select(['id'])->get();
                        if (count($cars)) {
                            $usadas[] = $palavra;
                        } else {
                            $cars = Carro::where('nome', 'like', "%$palavra%")->select(['id'])->get();
                            if (count($cars)) {
                                $usadas[] = $palavra;
                            }
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
