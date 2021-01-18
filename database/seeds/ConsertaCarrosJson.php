<?php

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

        $best_carros = [];
        $anterior = "";
        $count = 0;

        foreach ($marcas as $marca) {
            foreach ($marca['carros'] as $nome) {
                $nome = $this->clearString($nome);

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

                    if (count($idsIguais) > count($idsSalvar)) {
                        $idsSalvar = $idsIguais;
                        $nomeSalvar = $atual;
                    }
                }
                if ($achou) {
                    continue;
                }

                $novo_carro = [
                    'nome' => strtoupper(trim($nomeSalvar)),
                    'fipe_ids' => $idsSalvar,
                    'marca_id' => $marca['id']
                ];

                $best_carros[] = $novo_carro;

                print_r($novo_carro);

                if ($count > 40) return;

                $count++;
            }
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
        $allowed = strtoupper('1234567890qwertyuiopasdfghjklçzxcvbnm -éáâãõêóáíúà');

        $splitted = str_split($str);

        $result = "";

        foreach ($splitted as $letter) {
            if(strpos($allowed, $letter) !== false){
                $result .= $letter;
            }
        }

        return $result;
    }
}
