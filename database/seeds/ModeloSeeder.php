<?php

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
        $queries = explode('INSERT', Storage::get('modelos.sql'));

        array_splice($queries, 0, 1);

        foreach ($queries as $query) {
            DB::unprepared("INSERT $query");
        }

        return;
    }
}
