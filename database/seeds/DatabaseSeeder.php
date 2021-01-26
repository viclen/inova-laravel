<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared('SET FOREIGN_KEY_CHECKS=0;');

        DB::unprepared(Storage::get('db_bkp.sql'));

        DB::unprepared('SET FOREIGN_KEY_CHECKS=1;');

        DB::beginTransaction();

        // $this->call(ModeloSeeder::class);
        // $this->call(ConsertaCarrosJson::class);

        // $this->call(UserSeeder::class);
        // $this->call(ClienteSeeder::class);
        // $this->call(CaracteristicaSeeder::class);
        // $this->call(CarroClienteSeeder::class);
        // $this->call(EstoqueSeeder::class);
        // $this->call(InteresseSeeder::class);
        // $this->call(RegraSeeder::class);

        // $this->call(CaracteristicaEstoqueSeeder::class);
        // $this->call(CaracteristicaInteresseSeeder::class);

        DB::commit();

        // $this->call(MarcaSeeder::class);
        // $this->call(CarroSeeder::class);

        // $this->call(FipeSeeder::class);
    }
}
