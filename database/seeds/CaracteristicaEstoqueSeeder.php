<?php

use App\Caracteristica;
use App\CaracteristicaEstoque;
use App\Estoque;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CaracteristicaEstoqueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $estoques = Estoque::all();

        $insert = [];

        foreach ($estoques as $estoque) {
            $caracteristicas = Caracteristica::with('opcoes')->get();

            foreach ($caracteristicas as $caracteristica) {
                if (random_int(1, 10) > 5) {
                    if ($caracteristica->tipo == 3 && count($caracteristica->opcoes)) {
                        $insert[] = [
                            'caracteristica_id' => $caracteristica->id,
                            'estoque_id' => $estoque->id,
                            'valor' => random_int(1, count($caracteristica->opcoes) - 1),
                        ];
                    } elseif ($caracteristica->tipo == 0) {
                        $insert[] = [
                            'caracteristica_id' => $caracteristica->id,
                            'estoque_id' => $estoque->id,
                            'valor' => Str::random(10),
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
                            'estoque_id' => $estoque->id,
                            'valor' => $valor,
                        ];
                    } elseif ($caracteristica->tipo == 2) {
                        $insert[] = [
                            'caracteristica_id' => $caracteristica->id,
                            'estoque_id' => $estoque->id,
                            'valor' => random_int(1000, 20000),
                        ];
                    } elseif ($caracteristica->tipo == 4) {
                        $insert[] = [
                            'caracteristica_id' => $caracteristica->id,
                            'estoque_id' => $estoque->id,
                            'valor' => random_int(0, 1),
                        ];
                    }
                }
            }
        }

        CaracteristicaEstoque::insert($insert);
    }
}
