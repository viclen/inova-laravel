<?php

use App\Modelo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ModeloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared('SET FOREIGN_KEY_CHECKS=0;');

        DB::unprepared(Storage::get('modelos.sql'));

        DB::unprepared('SET FOREIGN_KEY_CHECKS=1;');

        Storage::put('modelos.json', json_encode(Modelo::all()));
    }
}
