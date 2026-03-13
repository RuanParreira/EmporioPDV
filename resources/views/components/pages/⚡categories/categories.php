<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

new class extends Component {
    public $categories = [];
    public int $totalCategories = 0;
    public int $totalProducts = 0;
    public string $topCategory = '-';

    // Rederizar a pagina
    public function render()
    {
        return $this->view()->layout('layouts.default', [
            'title' => 'Categorias',
        ]);
    }

    //Buscar as Categorias
    private function loadCategories(): void
    {
        $this->categories = Category::query()
            ->withCount('products')
            ->where(function ($query) {
                $query->where('name', '!=', 'SemCategoria')
                    ->orWhere(function ($q) {
                        $q->where('name', 'SemCategoria')->whereHas('products');
                    });
            })
            ->orderBy('name')
            ->get();

        $this->totalCategories = Category::where('name', '!=', 'SemCategoria')->count();
        $this->totalProducts = Product::count();

        $top = Category::withCount('products')->where('name', '!=', 'SemCategoria')->orderByDesc('products_count')->first();

        $this->topCategory = $top && $top->products_count > 0 ? "{$top->name} ({$top->products_count})" : '-';
    }

    public function mount(): void
    {
        $this->loadCategories();
    }

    // Deletar Categoria

    public function delete(Category $category): void
    {
        Gate::authorize('delete', $category);

        $defaultCategory = Category::firstOrCreate([
            'name' => 'SemCategoria',
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

        $this->loadCategories();
    }
};
