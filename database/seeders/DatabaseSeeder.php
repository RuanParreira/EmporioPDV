<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(20)->create();

        // Cria 5 categorias e, PARA CADA categoria criada, cria automaticamente 5 produtos.
        Category::factory(5)->has(Product::factory()->count(5))->create();

        User::factory()->create([
            'name' => 'dev',
            'email' => 'test@test.com',
            'password' => 'Password@123',
            'role' => 'dev'
        ]);

        User::factory()->create([
            'name' => 'owner',
            'email' => 'owner@owner.com',
            'password' => 'Password@123',
            'role' => 'owner'
        ]);
        User::factory()->create([
            'name' => 'adm',
            'email' => 'adm@adm.com',
            'password' => 'Password@123',
            'role' => 'admin'
        ]);
        User::factory()->create([
            'name' => 'caixa',
            'email' => 'caixa@caixa.com',
            'password' => 'Password@123'
        ]);
    }
}
