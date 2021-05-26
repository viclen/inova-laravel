<?php

use App\Carro;
use App\Marca;
use App\Modelo;
use Illuminate\Database\Seeder;
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
        $ultima_execucao = Storage::get("ultima_execucao.txt");

        if ($ultima_execucao && time() - intval($ultima_execucao) < 2 * 24 * 60 * 60) {
            echo "Última execução em menos de 2 dias";
            return;
        }

        Storage::put("ultima_execucao.txt", time());

        $ultima_marca = Storage::get("ultima_marca.txt");
        $ultimo_carro = Storage::get("ultimo_carro.txt");

        echo "Inicio: " . now() . "\r\n\r\n";

        $max_erros = 3;

        $erro_marcas = false;

        do {
            try {
                $url = "http://fipeapi.appspot.com/api/1/carros/marcas.json";
                $array_marcas = json_decode(file_get_contents($url), true);
                sleep(1);

                if ($ultima_marca) {
                    foreach ($array_marcas as $i => $dados_marca) {
                        if ($i < count($array_marcas) - 1) {
                            if ($dados_marca['id'] == $ultima_marca) {
                                $array_marcas = array_splice($array_marcas, $i);
                                break;
                            }
                        }
                    }
                }

                foreach ($array_marcas as $dados_marca) {
                    $marca = Marca::find($dados_marca['id']);
                    $msg = "";
                    if (!$marca) {
                        $marca = new Marca();
                        $marca->id = $dados_marca['id'];
                        $marca->key = $dados_marca['key'];
                        $msg = ": Nova";
                    }
                    $marca->nome = $dados_marca['fipe_name'];
                    $marca->save();

                    echo $this->tab(0) . $marca->nome . " [$marca->id] $msg\r\n";

                    $url = "http://fipeapi.appspot.com/api/1/carros/veiculos/$marca->id.json";
                    try {
                        $carros = json_decode(file_get_contents($url), true);
                    } catch (Exception $e) {
                        continue;
                    }
                    sleep(1);

                    if ($ultimo_carro) {
                        foreach ($carros as $i => $carro_fipe) {
                            if ($i < count($carros) - 1) {
                                if ($carro_fipe['id'] == $ultimo_carro) {
                                    $carros = array_splice($carros, $i + 1);
                                    break;
                                }
                            }
                        }
                    }

                    $erros_carro = 0;
                    foreach ($carros as $carro_fipe) {
                        $carro_fipe_id = $carro_fipe['id'];

                        $carro = Carro::where("fipe_ids", "like", "%\"$carro_fipe_id\"%")->first();
                        $msg = "";

                        if (!$carro) {
                            $nome = $this->clearString($carro_fipe['name']);

                            $palavras = explode(' ', $nome);

                            $atual = '';

                            foreach ($palavras as $palavra) {
                                $atual = trim($atual . ' ' . $palavra);

                                $modelo = Modelo::where('nome', $marca->id)->where("nome", "like", "$atual")->first();

                                if ($modelo) {
                                    $carro = Carro::find($modelo->carro_id);
                                } else {
                                    $carro_atual = Carro::where('marca_id', $marca->id)->where(function ($query) use ($atual) {
                                        $query->where("nome", "like", "$atual")
                                            ->orWhere("nome", "like", "$atual %");
                                    })->first();

                                    if ($carro_atual) {
                                        $carro = $carro_atual;
                                    }
                                }
                            }

                            if (!$carro) {
                                $carro = new Carro();
                                $carro->fipe_ids = "[\"$carro_fipe_id\"]";
                                $carro->nome = $this->clearString($carro_fipe['name']);
                                $carro->marca_id = $marca->id;
                                $carro->save();
                                $msg = ": Novo";
                            } else {
                                $fipe_ids = json_decode($carro->fipe_ids);

                                if (array_search("$carro_fipe_id", $fipe_ids) === false) {
                                    $fipe_ids[] = "$carro_fipe_id";
                                    $carro->fipe_ids = json_encode($fipe_ids);
                                    $carro->save();
                                }

                                $msg = ": Encontrado";
                            }
                        }

                        echo $this->tab(1) . $carro->nome . " [$carro_fipe_id] $msg\r\n";

                        try {
                            $url = "http://fipeapi.appspot.com/api/1/carros/veiculo/$marca->id/$carro_fipe_id.json";
                            $dados_fipe = json_decode(file_get_contents($url), true);
                            sleep(1);

                            $erros_modelo = 0;

                            $modelos_buscados = [];
                            for ($cont = 0; $cont < count($dados_fipe); $cont++) {
                                foreach ($dados_fipe as $dados) {
                                    try {
                                        $modelo_id = $dados['id'];

                                        if (array_search($modelo_id, $modelos_buscados) === false) {
                                            $url = "http://fipeapi.appspot.com/api/1/carros/veiculo/$marca->id/$carro_fipe_id/$modelo_id.json";
                                            $json = file_get_contents($url);
                                            $dados_modelo = json_decode($json, true);
                                            $dados_modelo['preco'] = $this->onlyNumbers($dados_modelo['preco']);

                                            $modelo = Modelo::where([['fipe_id', $modelo_id], ['carro_id', $carro->id]])->first();
                                            $msg = "";
                                            if (!$modelo) {
                                                $modelo = new Modelo();
                                                $modelo->ano = $dados_modelo['ano_modelo'];
                                                $modelo->combustivel = substr($dados_modelo['combustivel'], 0, 1);
                                                $modelo->carro_id = $carro->id;
                                                $modelo->fipe_id = $modelo_id;
                                                $modelo->nome = $dados_modelo['name'];
                                                $modelo->preco = 0;
                                                $msg = ": Novo";
                                            }
                                            if ($modelo->preco != $dados_modelo['preco']) {
                                                if (!$msg)
                                                    $msg = ": Atualizado | $modelo->preco -> $dados_modelo[preco]";
                                                $modelo->preco = $dados_modelo['preco'];
                                                $modelo->referencia = $dados_modelo['referencia'];
                                            }
                                            $modelo->save();

                                            $erros_modelo = 0;
                                            $modelos_buscados[] = $modelo->fipe_id;

                                            sleep(1);

                                            echo $this->tab(2) . $modelo->fipe_id . "$msg \r\n";
                                        }
                                    } catch (\Throwable $th) {
                                        throw $th;
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

                            Storage::put("ultimo_carro.txt", $carro_fipe_id);
                        } catch (Exception $e) {
                            echo $this->tab(3) . "Erro " . ($erros_carro + 1) . "/$max_erros: " . str_replace(['file_get_contents', '(', ')'], '', $th->getMessage());
                            if ($erros_carro + 1 < $max_erros) {
                                sleep(10);
                                $erros_carro++;
                            } else {
                                $erros_carro = 0;
                            }
                        }
                    }

                    Storage::put("ultima_marca.txt", $marca->id);
                }

                echo "\r\nFim: " . now() . "\r\n";
            } catch (Exception $e) {
                $erro_marcas = true;

                sleep(60);
            }
        } while ($erro_marcas);
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

    public function clearString($str)
    {
        $allowed = strtoupper('1234567890qwertyuiopasdfghjklçzxcvbnm. -éáâãõêóáíúà');

        $splitted = str_split(strtoupper($str));

        $result = "";

        foreach ($splitted as $letter) {
            if (($letter || $letter == 0) && strpos($allowed, $letter) !== false) {
                $result .= $letter;
            } else {
                $result .= "-";
            }
        }

        return $result;
    }
}
