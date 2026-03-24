<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->nullable()
                ->unique();
            $table->foreignId('category_id')->nullable()
                ->constrained('categories') //Relacionamento do ID
                ->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2); // 10 dígitos totais, 2 casas decimais
            $table->string('measure_unit', 2)->default('UN'); // UN ou KG
            $table->boolean('active')->default(true); // Produto começa ativo
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
