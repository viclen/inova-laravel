<?php

use App\Marca;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class MarcaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $marcas = [];

        $data = Storage::get('marcas_carros.json');

        $array_marcas = json_decode($data, true);

        foreach ($array_marcas as $marca) {
            $marcas[] = [
                'nome' => $marca['name'],
                'fipe_id' => $marca['id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Marca::insert($marcas);

        return true;

        // request

        // $data = file_get_contents("http://fipeapi.appspot.com/api/1/carros/marcas.json");

        // $array_marcas = json_decode($data, true);

        // foreach ($array_marcas as $dados_marca) {
        //     try {
        //         $id = $dados_marca['id'];
        //         $array_carros = json_decode(file_get_contents("http://fipeapi.appspot.com/api/1/carros/veiculos/$id.json"), true);
        //         $marca_json = [];
        //         foreach ($array_carros as $carro) {
        //             $name = $carro['name'];
        //             $dot = strpos($name, '.');
        //             if ($dot) {
        //                 $name = trim(substr($name, 0, $dot - 1));
        //             }
        //             if (array_search($name, $marca_json) === false) {
        //                 $marca_json[$carro['id']] = $name;
        //             }
        //         }

        //         echo "$dados_marca[name]: " . count($marca_json) . "\n";

        //         $dados_marca['carros'] = $marca_json;

        //         $marcas[] = [
        //             'nome' => $dados_marca['name'],
        //             'fipe_json' => json_encode($dados_marca),
        //             'created_at' => now(),
        //             'updated_at' => now(),
        //         ];
        //     } catch (Exception $e) {
        //     }
        // }

        // Storage::put('marcas_fipe.json', json_encode($marcas));
    }
}
