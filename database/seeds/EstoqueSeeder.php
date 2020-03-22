<?php

use App\Carro;
use App\Estoque;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EstoqueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dados_salvos = json_decode(Storage::get('fipe_valor.json'), true);

        $carros = Carro::with('marca')->get();

        $estoque = [];

        $cores = ['branco', 'preto', 'prata', 'vermelho', 'azul', 'verde', 'amarelo', 'dourado'];

        $porcentagem = 0;
        $max = count($carros);
        echo "Carregando dados para o estoque: \n";
        echo "[";
        foreach ($carros as $i => $carro) {
            if ($carro->fipe_id && $carro->marca && random_int(0, 10) > 8) {
                $marca = $carro->marca->fipe_id;
                try {
                    $valor = 0;
                    if (isset($dados_salvos[$carro->fipe_id])) {
                        $valor = intval($dados_salvos[$carro->fipe_id]);
                    } else {
                        $url = "http://fipeapi.appspot.com/api/1/carros/veiculo/$marca/$carro->fipe_id.json";
                        $dados_fipe = json_decode(file_get_contents($url), true);

                        $fipe_id = $dados_fipe[0]['id'];
                        $url = "http://fipeapi.appspot.com/api/1/carros/veiculo/$marca/$carro->fipe_id/$fipe_id.json";
                        $dados_fipe = json_decode(file_get_contents($url), true);

                        $valor = $this->onlyNumbers($dados_fipe['preco']);
                        $dados_salvos[$carro->fipe_id] = $valor;
                    }

                    $estoque[] = [
                        'carro_id' => $carro->id,
                        'cor' => $cores[random_int(0, count($cores) - 1)],
                        'fipe' => $valor,
                        'valor' => ($valor + random_int(($valor / -10), ($valor / 10))),
                        'ano' => isset($fipe_id) && $fipe_id ? explode('-', $fipe_id)[0] : random_int(1999, 2010),
                        'placa' => "" . chr(random_int(65, 90)) . chr(random_int(65, 90)) . chr(random_int(65, 90)) . "-" . random_int(1111, 9999),
                        'chassi' => md5(uniqid()),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                } catch (Exception $e) {
                }
            }

            $p = round(100 * $i / $max);
            if ($p % 5 === 0 && $p != $porcentagem) {
                $porcentagem = $p;
                echo ".";
            }
        }
        echo "] 100%\n";

        Estoque::insert($estoque);

        Storage::put('fipe_valor.json', json_encode($dados_salvos));
    }

    public function onlyNumbers($in)
    {
        $allowed = "1234567890";

        $i = 0;
        $out = "";
        $char = substr($in, $i++, 1);
        while ($char != null) {
            if (strpos($allowed, "$char") !== false) {
                $out .= "$char";
            } elseif ($char === ",") {
                return intval($out);
            }
            $char = substr($in, $i++, 1);
        }

        return intval($out);
    }
}
