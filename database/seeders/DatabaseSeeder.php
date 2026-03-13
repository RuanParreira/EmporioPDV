<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
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
        // User::factory(10)->create();

        Category::firstOrCreate(['name' => 'SemCategoria']);

        User::factory()->create([
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => 'Password@123',
            'role' => 'dono'
        ]);
        User::factory()->create([
            'name' => 'adm',
            'email' => 'adm@adm.com',
            'password' => 'Password@123',
            'role' => 'adm'
        ]);
        User::factory()->create([
            'name' => 'caixa',
            'email' => 'caixa@caixa.com',
            'password' => 'Password@123'
        ]);
    }
}
