<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PriceSeeder extends Seeder
{
    public function run()
    {
        // Preços para carros
        DB::table('price_cars')->insert([
            'valorHora' => 5.00,
            'valorMinimo' => 10.00,
            'valorDiaria' => 50.00,
            'taxaAdicional' => 17.00,
            'taxaMensal' => 400.00,
            'updated_at' => now()
        ]);

        // Preços para motos
        DB::table('price_motorcycles')->insert([
            'valorHora' => 5.00,
            'valorMinimo' => 3.00,
            'valorDiaria' => 25.00,
            'taxaAdicional' => 10.00,
            'taxaMensal' => 200.00,
            'updated_at' => now()
        ]);

        // Preços para caminhões
        DB::table('price_trucks')->insert([
            'valorHora' => 15.00,
            'valorMinimo' => 10.00,
            'valorDiaria' => 80.00,
            'taxaAdicional' => 25.00,
            'taxaMensal' => 600.00,
            'updated_at' => now()
        ]);
    }
} 