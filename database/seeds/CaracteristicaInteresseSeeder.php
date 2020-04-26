<?php

use App\Caracteristica;
use App\CaracteristicaInteresse;
use App\Interesse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CaracteristicaInteresseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $comparadores = [
            '<',
            '>',
            '=',
            '~',
        ];

        $interesses = Interesse::all();

        $insert = [];

        foreach ($interesses as $interesse) {
            $caracteristicas = Caracteristica::with('opcoes')->get();

            foreach ($caracteristicas as $caracteristica) {
                if (random_int(1, 10) > 5) {
                    if ($caracteristica->tipo == 3 && count($caracteristica->opcoes)) {
                        $insert[] = [
                            'caracteristica_id' => $caracteristica->id,
                            'interesse_id' => $interesse->id,
                            'valor' => random_int(1, count($caracteristica->opcoes) - 1),
                            'comparador' => '='
                        ];
                    } elseif ($caracteristica->tipo == 0) {
                        $insert[] = [
                            'caracteristica_id' => $caracteristica->id,
                            'interesse_id' => $interesse->id,
                            'valor' => Str::random(10),
                            'comparador' => '=',
                        ];
                    } elseif ($caracteristica->tipo == 1) {
                        $valor = random_int(1000, 9999);
                        if ($caracteristica->nome == 'ano') {
                            $valor = random_int(1995, 2020);
                        } elseif ($caracteristica->nome == 'km') {
                            $valor = random_int(10000, 100000);
                        }
                        $insert[] = [
                            'caracteristica_id' => $caracteristica->id,
                            'interesse_id' => $interesse->id,
                            'valor' => $valor,
                            'comparador' => $comparadores[random_int(0, count($comparadores) - 1)]
                        ];
                    } elseif ($caracteristica->tipo == 2) {
                        $insert[] = [
                            'caracteristica_id' => $caracteristica->id,
                            'interesse_id' => $interesse->id,
                            'valor' => random_int(1000, 30000),
                            'comparador' => $comparadores[random_int(0, count($comparadores) - 1)]
                        ];
                    } elseif ($caracteristica->tipo == 4) {
                        $insert[] = [
                            'caracteristica_id' => $caracteristica->id,
                            'interesse_id' => $interesse->id,
                            'valor' => random_int(0, 1),
                            'comparador' => '='
                        ];
                    }
                }
            }
        }

        CaracteristicaInteresse::insert($insert);
    }
}
