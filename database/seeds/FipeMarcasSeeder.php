<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class FipeMarcasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array_marcas = json_decode(Storage::get('marcas.json'), true);

        $marcasSalvar = [];

        foreach ($array_marcas as $dados_marca) {
            $marca = [
                'id' => $dados_marca['id'],
                'name' => strtoupper($dados_marca['fipe_name']),
                'key' => $dados_marca['key'],
                'carros' => []
            ];

            echo "$marca[id] - $marca[name]\n";

            $url = "http://fipeapi.appspot.com/api/1/carros/veiculos/$marca[id].json";
            $carros = json_decode(file_get_contents($url), true);

            foreach ($carros as $carro) {
                $marca['carros'][$carro['id']] = $carro['name'];
            }

            $marcasSalvar[] = $marca;

            sleep(1);
        }

        Storage::put('marcas_carros.json', json_encode($marcasSalvar));
    }
}
