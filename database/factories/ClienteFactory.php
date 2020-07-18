<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Cliente;
use Faker\Generator as Faker;

$factory->define(Cliente::class, function (Faker $faker) {
    $fakephone = "(" . random_int(10, 99) . ") " . random_int(10000, 99999) . "-" . random_int(1000, 9999);
    return [
        'nome' => $faker->name,
        'telefone' => $fakephone,
        'endereco' => "Rua $faker->name, " . random_int(10, 200),
        'cidade' => $faker->city,
        'email' => $faker->safeEmail,
        'created_at' => now(),
        'updated_at' => now(),
    ];
});
