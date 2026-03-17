<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;

new #[Layout('layouts.default')] #[Title('Lista de Categorias')] class extends Component {
    use WithPagination;

    public int $totalCategories = 0;
    public int $totalProducts = 0;
    public string $topCategory = '-';

    public function mount(): void
    {
        $this->loadStats();
    }

    //Buscar as Categorias
    #[On('category-saved')]
    public function loadStats(): void
    {
        $this->totalProducts = Product::count();
        $this->totalCategories = Category::where('name', '!=', Category::DEFAULT_NAME)->count();

        $top = Category::withCount('products')->orderByDesc('products_count')->first();
        $this->topCategory = ($top && $top->products_count > 0)
            ? "{$top->name} ({$top->products_count})"
            : '-';
    }

    #[Computed]
    public function categories()
    {
        return Category::query()
            ->withCount('products')
            ->where('name', '!=', Category::DEFAULT_NAME)
            ->orWhere(fn($q) => $q->where('name', Category::DEFAULT_NAME)->has('products'))
            ->orderBy('id')
            ->paginate(18); // <-- Paginação com 18 itens
    }

    // Deletar Categoria
    public function delete(Category $category): void
    {
        Gate::authorize('delete', $category);

        $defaultCategory = Category::firstOrCreate([
            'name' => Category::DEFAULT_NAME,
        ]);

        if ($category->id === $defaultCategory->id) {
            $this->addError('delete', 'A categoria padrão não pode ser removida.');
            return;
        }

        DB::transaction(function () use ($category, $defaultCategory) {
            $category->products()->update([
                'category_id' => $defaultCategory->id,
            ]);

            $category->delete();
        });

        $this->loadStats();
    }
};
