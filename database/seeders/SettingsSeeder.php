<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Settings;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        Settings::create([
            'nome_da_empresa' => 'Estacionamento',
            'endereco' => 'Rua Principal',
            'cidade' => 'Sua Cidade',
            'estado' => 'SP',
            'cep' => '00000-000',
            'telefone_da_empresa' => '(00) 0000-0000',
            'email_da_empresa' => 'contato@estacionamento.com',
            'numero_de_registro_da_empresa' => '123456',
            'cnpj_cpf_da_empresa' => '00.000.000/0000-00',
            'descricao_da_empresa' => 'Sistema de Gerenciamento de Estacionamento',
            'coordenadas_gps' => '-23.550520, -46.633308'
        ]);
    }
} 