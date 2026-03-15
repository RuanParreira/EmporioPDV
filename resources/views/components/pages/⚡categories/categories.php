<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

new class extends Component {
    public Collection $categories;
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


    public function mount(): void
    {
        // Agora o loadCategories vai preencher a Collection
        $this->loadCategories();
    }
    //Buscar as Categorias
    private function loadCategories(): void
    {
        // Listagem para os cards
        $this->categories = Category::query()
            ->withCount('products')
            ->where('name', '!=', Category::DEFAULT_NAME)
            ->orWhere(fn($q) => $q->where('name', Category::DEFAULT_NAME)->has('products'))
            ->orderBy('name')
            ->get();

        // Estatísticas (calculadas a partir da coleção já carregada para poupar banco)
        $this->totalProducts = Product::count();
        $this->totalCategories = $this->categories->where('name', '!=', Category::DEFAULT_NAME)->count();

        $top = $this->categories->sortByDesc('products_count')->first();
        $this->topCategory = ($top && $top->products_count > 0)
            ? "{$top->name} ({$top->products_count})"
            : '-';
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

        $this->loadCategories();
    }
};
