<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Enterprise;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $enterprise = Enterprise::create([
            'name' => 'Empório Principal',
            'cnpj' => '65468243000102',
            'number' => '7781662230',
            'address' => 'Avenida Claudino Barretos Rios, N° 7 - Centro',
            'is_active' => true,
        ]);
        // Segunda empresa fictícia
        $enterprise2 = Enterprise::create([
            'name' => 'Mercado Central',
            'cnpj' => '12345678000199',
            'number' => '9988776655',
            'address' => 'Rua das Flores, 123 - Centro',
            'is_active' => true,
        ]);

        $user2 = User::factory()->create([
            'enterprise_id' => $enterprise2->id,
            'name' => 'João Silva',
            'email' => 'joao@mercado.com',
            'password' => 'Password@123',
            'role' => 'owner'
        ]);

        $admin2 = User::factory()->create([
            'enterprise_id' => $enterprise2->id,
            'name' => 'Maria Admin',
            'email' => 'admin@mercado.com',
            'password' => 'Password@123',
            'role' => 'admin'
        ]);

        $caixa2 = User::factory()->create([
            'enterprise_id' => $enterprise2->id,
            'name' => 'Carlos Caixa',
            'email' => 'caixa@mercado.com',
            'password' => 'Password@123',
            'role' => 'caixa'
        ]);

        Category::factory(3)->create(['enterprise_id' => $enterprise2->id])->each(function ($category) use ($enterprise2) {
            Product::factory(4)->create([
                'enterprise_id' => $enterprise2->id,
                'category_id' => $category->id
            ]);
        });

        $produtosDestaEmpresa2 = Product::where('enterprise_id', $enterprise2->id)->get();

        Sale::factory(5)->create([
            'enterprise_id' => $enterprise2->id,
            'user_id' => $caixa2->id
        ])->each(function ($sale) use ($produtosDestaEmpresa2) {
            $produtosAleatorios = $produtosDestaEmpresa2->random(2);
            foreach ($produtosAleatorios as $produto) {
                SaleItem::factory()->create([
                    'sale_id' => $sale->id,
                    'product_id' => $produto->id,
                    'product_name' => $produto->name,
                    'unit_price' => $produto->price,
                ]);
            }
        });

        User::factory()->create([
            'enterprise_id' => null,
            'name' => 'Ruan',
            'email' => 'test@test.com',
            'password' => 'Password@123',
            'role' => 'dev'
        ]);

        User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'name' => 'Thalia',
            'email' => 'owner@owner.com',
            'password' => 'Password@123',
            'role' => 'owner'
        ]);

        $caixa = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'name' => 'adm',
            'email' => 'adm@adm.com',
            'password' => 'Password@123',
            'role' => 'admin'
        ]);

        User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'name' => 'caixa',
            'email' => 'caixa@caixa.com',
            'password' => 'Password@123',
            'role' => 'caixa'
        ]);

        Category::factory(5)->create(['enterprise_id' => $enterprise->id])->each(function ($category) use ($enterprise) {
            Product::factory(5)->create([
                'enterprise_id' => $enterprise->id,
                'category_id' => $category->id
            ]);
        });

        $produtosDestaEmpresa = Product::where('enterprise_id', $enterprise->id)->get();

        Sale::factory(10)->create([
            'enterprise_id' => $enterprise->id,
            'user_id' => $caixa->id
        ])->each(function ($sale) use ($produtosDestaEmpresa) {
            // Pegar 3 produtos aleatórios dessa empresa e criar os itens da venda
            $produtosAleatorios = $produtosDestaEmpresa->random(3);

            foreach ($produtosAleatorios as $produto) {
                SaleItem::factory()->create([
                    'sale_id' => $sale->id,
                    'product_id' => $produto->id,
                    'product_name' => $produto->name,
                    'unit_price' => $produto->price,
                ]);
            }
        });
    }
}
