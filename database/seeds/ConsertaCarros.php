<?php

use App\Carro;
use App\Modelo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ConsertaCarros extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared('
            UPDATE carros SET nome = REPLACE(nome, "/", " / ") WHERE nome LIKE "%/%" AND nome NOT LIKE "% / %";
            UPDATE carros SET nome = REPLACE(nome, "  ", " ") WHERE nome LIKE "%  %";
            UPDATE modelos SET nome = REPLACE(nome, "/", " / ") WHERE nome LIKE "%/%" AND nome NOT LIKE "% / %";
            UPDATE modelos SET nome = REPLACE(nome, "  ", " ") WHERE nome LIKE "%  %";
        ');

        $modelos = Modelo::with('carro')->where('nome', '')->get();

        foreach ($modelos as $modelo) {
            if (!$modelo->nome) {
                $modelo->nome = $modelo->carro->nome;
                $modelo->save();
            }
        }

        $best_carros = [];
        $carros = Carro::orderBy('nome')->get();

        foreach ($carros as $carro) {
            $palavras = explode(' ', $carro->nome);

            $achou = false;
            $atual = '';
            foreach ($palavras as $palavra) {
                $atual = trim($atual . ' ' . $palavra);
                if (count(array_filter($best_carros, function ($c) use ($atual) {
                    return strtoupper($c['nome']) == strtoupper($atual);
                })) > 0) {
                    $achou = true;
                    break;
                }
            }
            if ($achou) {
                continue;
            }

            $anterior = '';
            $cont_atual = 0;
            $atual = '';
            $marca = $carro->marca_id;
            $carros_iguais = [];
            $carros_iguais_anterior = [];
            foreach ($palavras as $i => $palavra) {
                $atual = trim($atual . ' ' . $palavra);

                $search = $i < count($palavras) - 1 ? "$atual %" : "$atual";

                $carros_iguais = Carro::whereRaw("nome like '$search'")->get()->toArray();

                $cont = count($carros_iguais);

                if ($cont >= $cont_atual) {
                    $cont_atual = $cont;
                    $anterior = $atual;
                    $carros_iguais_anterior = $carros_iguais;
                } else {
                    break;
                }
            }

            $marcas = ["$carro->marca_id" => 0];

            foreach ($carros_iguais_anterior as $car) {
                if ($car['marca_id'] == 0) {
                    continue;
                }

                if (isset($marcas[$car['marca_id']])) {
                    $marcas[$car['marca_id']]++;
                } else {
                    $marcas[$car['marca_id']] = 1;
                }
            }

            $marca = array_keys($marcas, max($marcas))[0];

            $carros_iguais_anterior[] = ['id' => $carro->id];

            $best_carros[] = [
                'nome' => strtoupper($anterior),
                'marca_id' => $marca,
                'fipe_ids' => array_map(function ($car) {
                    return "$car[id]";
                }, $carros_iguais_anterior)
            ];

            echo "$anterior | $marca | $cont_atual | " . json_encode(array_map(function ($car) {
                return $car['id'];
            }, $carros_iguais_anterior)) . "\n";

            // echo '.';
        }

        Storage::put('best_carros.json', json_encode($best_carros));

        DB::beginTransaction();
        DB::unprepared('SET FOREIGN_KEY_CHECKS=0;');

        Carro::query()->truncate();

        Carro::insert(array_map(function ($car) {
            echo '.';
            $car['fipe_ids'] = json_encode($car['fipe_ids']);
            return $car;
        }, $best_carros));

        echo "\n\n\n";

        foreach ($best_carros as $carro) {
            echo '.';
            $id = Carro::where('nome', $carro['nome'])->first()->id;

            Modelo::whereIn('id', $carro['fipe_ids'])->update(['carro_id' => $id]);
        }

        // DB::rollBack();
        DB::commit();
        DB::unprepared('SET FOREIGN_KEY_CHECKS=1;');

        $modelos = Modelo::with('carro')->get();

        foreach ($modelos as $modelo) {
            if ($modelo->carro == null) {
                $carro = Carro::whereRaw("'$modelo->nome' LIKE CONCAT(carros.nome, ' %') OR '$modelo->nome' LIKE carros.nome")->first();

                if ($carro) {
                    $modelo->carro_id = $carro->id;
                    $modelo->save();
                } else {
                    echo "Cleaning $modelo->id: $modelo->nome \n";
                    $modelo->delete();
                }
            }
        }
    }
}
