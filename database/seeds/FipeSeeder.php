<?php

use App\Carro;
use App\Estoque;
use App\FipeCarro;
use App\FipeMarca;
use App\FipeModelo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $nao_existem = json_decode(Storage::get('naoexistem.json'));

        echo count($nao_existem);

        return;
        $max_erros = 3;

        $data = Storage::get('marcas_carros.json');

        $array_marcas = json_decode($data, true);

        foreach ($array_marcas as $dados_marca) {
            $marca = new FipeMarca([
                'id' => $dados_marca['id'],
                'key' => $dados_marca['key'],
                'nome' => $dados_marca['fipe_name'],
            ]);
            $marca->save();

            echo $this->tab(0) . $marca->nome . "\r\n";

            $carros = $dados_marca['carros'];

            $erros_carro = 0;
            foreach ($carros as $carro_id => $carro_nome) {
                $carro = new FipeCarro([
                    'id' => $carro_id,
                    'nome' => $carro_nome,
                    'fipe_marca_id' => $marca->id
                ]);
                $carro->save();

                echo $this->tab(1) . $carro->nome . "\r\n";

                try {
                    $url = "http://fipeapi.appspot.com/api/1/carros/veiculo/$marca->id/$carro->id.json";
                    $dados_fipe = json_decode(file_get_contents($url), true);
                    sleep(1);

                    $erros_modelo = 0;

                    $modelos_buscados = [];
                    for ($cont = 0; $cont < count($dados_fipe); $cont++) {
                        foreach ($dados_fipe as $dados) {
                            try {
                                $modelo_id = $dados['id'];

                                if (array_search($modelo_id, $modelos_buscados) === false) {
                                    echo $this->tab(2) . $modelo_id . "\r\n";

                                    $url = "http://fipeapi.appspot.com/api/1/carros/veiculo/$marca->id/$carro->id/$modelo_id.json";
                                    $json = file_get_contents($url);
                                    $dados_modelo = json_decode($json, true);
                                    $dados_modelo['preco'] = $this->onlyNumbers($dados_modelo['preco']);
                                    $modelo = new FipeModelo([
                                        'fipe_id' => $modelo_id,
                                        'preco' => $this->onlyNumbers($dados_modelo['preco']),
                                        'ano' => $dados_modelo['ano_modelo'],
                                        'combustivel' => substr($dados_modelo['combustivel'], 0, 1),
                                        'dados' => $json,
                                        'fipe_carro_id' => $carro->id,
                                    ]);
                                    $modelo->save();

                                    $erros_modelo = 0;
                                    $modelos_buscados[] = $modelo_id;

                                    sleep(1);
                                }
                            } catch (\Throwable $th) {
                                echo $this->tab(3) . "Erro " . ($erros_modelo + 1) . "/$max_erros: $url";

                                if (strpos($th->getMessage(), "403")) {
                                    sleep(51);
                                }

                                if ($erros_modelo + 1 < $max_erros) {
                                    sleep(10);
                                    $erros_modelo++;
                                    $cont--;
                                } else {
                                    $erros_modelo = 0;
                                }
                            }
                        }
                    }
                } catch (Exception $e) {
                    echo $this->tab(3) . "Erro " . ($erros_carro + 1) . "/$max_erros: " . str_replace(['file_get_contents', '(', ')'], '', $th->getMessage());
                    if ($erros_carro + 1 < $max_erros) {
                        sleep(10);
                        $erros_carro++;
                        $cont--;
                    } else {
                        $erros_carro = 0;
                    }
                }
            }
        }
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

    public function tab($qnt)
    {
        $r = "";
        for ($j = 0; $j < $qnt; $j++) {
            $r .= "\t";
        }
        return $r;
    }
}
