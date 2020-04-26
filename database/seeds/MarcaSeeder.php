<?php

use App\Marca;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        DB::unprepared(Storage::get('marcas.sql'));

        return;

        // $marcas = [];

        // $data = Storage::get('marcas_carros.json');

        // $array_marcas = json_decode($data, true);

        // foreach ($array_marcas as $marca) {
        //     $marcas[] = [
        //         'nome' => $marca['name'],
        //         'fipe_id' => $marca['id'],
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ];
        // }

        // Marca::insert($marcas);

        // return true;
    }
}
