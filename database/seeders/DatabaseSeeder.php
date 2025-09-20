<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Limpa as tabelas na ordem correta (filhas para pais)
        DB::table('users')->truncate();
        DB::table('sale_items')->truncate();
        DB::table('inventory')->truncate();
        DB::table('sales')->truncate();
        DB::table('products')->truncate();
        
        // Reativa a verificação de chaves estrangeiras

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            ProductSeeder::class,
        ]);
                
    }
}