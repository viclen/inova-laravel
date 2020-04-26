<?php

use App\Carro;
use App\Estoque;
use App\Modelo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        $carros = Carro::all();

        $estoque = [];

        $porcentagem = 0;
        $max = count($carros);
        echo "Carregando dados para o estoque: \n";
        echo "[";
        foreach ($carros as $i => $carro) {
            if (random_int(0, 10) > 8) {
                $modelo = Modelo::where('carro_id', $carro->id)->inRandomOrder()->first();
                $estoque[] = [
                    'carro_id' => $carro->id,
                    'modelo_id' => $modelo ? $modelo->id : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $p = round(100 * $i / $max);
            if ($p % 5 === 0 && $p != $porcentagem) {
                $porcentagem = $p;
                echo ".";
            }
        }
        echo "] 100%\n";

        Estoque::insert($estoque);
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
