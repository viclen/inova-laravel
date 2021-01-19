<?php

use App\Carro;
use App\Marca;
use App\Modelo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ConsertaCarrosJson extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $marcas = json_decode(Storage::get('marcas_carros.json'), true);
        $marcasSalvar = [[
            'id' => 0,
            'nome' => 'Outra',
            'key' => null
        ]];

        $best_carros = [];
        $count = 0;

        foreach ($marcas as $marca) {
            $marcasSalvar[] = [
                'id' => $marca['id'],
                'nome' => $marca['name'],
                'key' => $marca['key']
            ];

            foreach ($marca['carros'] as $nome) {
                $nome = $this->clearString($nome);

                // echo "$nome\n";

                $palavras = explode(' ', $nome);

                $achou = false;
                $atual = '';
                $nomeSalvar = "";

                $idsSalvar = [];
                foreach ($palavras as $palavra) {
                    $atual = trim($atual . ' ' . $palavra);
                    if (count(array_filter($best_carros, function ($c) use ($atual) {
                        return strtoupper($c['nome']) == strtoupper($atual);
                    })) > 0) {
                        $achou = true;
                        break;
                    }

                    $idsIguais = $this->idsIguais($atual, $marca['carros']);

                    if (count($idsIguais) >= count($idsSalvar)) {
                        $idsSalvar = $idsIguais;
                        $nomeSalvar = $atual;
                    }
                }
                if ($achou) {
                    continue;
                }

                $novo_carro = [
                    'nome' => strtoupper(trim($nomeSalvar)),
                    'fipe_ids' => json_encode($idsSalvar),
                    'marca_id' => $marca['id']
                ];

                $best_carros[] = $novo_carro;

                $count++;
            }
        }

        Marca::insert($marcasSalvar);
        Carro::insert($best_carros);

        $carros = Carro::all();

        foreach ($carros as $carro) {
            Modelo::whereIn('carro_id', json_decode($carro->fipe_ids))->update(['carro_id' => $carro->id]);
        }
    }

    public function idsIguais($nome, $carros = [])
    {
        $ids = [];

        foreach ($carros as $id => $carro) {
            $carro = $this->clearString($carro);
            if (preg_match("/^" . strtoupper($nome) . "\W/", strtoupper($carro)) || strtoupper($carro) === strtoupper($nome)) {
                $ids[] = $id;
            }
        }

        return $ids;
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
