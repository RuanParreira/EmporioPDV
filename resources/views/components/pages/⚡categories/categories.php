<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;

new #[Layout('layouts.default')] #[Title('Lista de Categorias')] class extends Component {
    use WithPagination;

    public int $totalCategories = 0;
    public int $totalProducts = 0;
    public string $topCategory = '-';
    public int $uncategorizedProducts = 0;

    public function mount(): void
    {
        $this->loadStats();
    }

    //Buscar as Categorias
    #[On('category-saved')]
    public function loadStats(): void
    {
        $this->totalProducts = Product::count();
        $this->totalCategories = Category::count();

        $this->uncategorizedProducts = Product::whereNull('category_id')->count();

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
            ->orderBy('id')
            ->paginate(18); // <-- Paginação com 18 itens
    }

    // Deletar Categoria
    public function delete(Category $category): void
    {
        Gate::authorize('delete', $category);

        $category->delete();

        $this->loadStats();
    }
};
