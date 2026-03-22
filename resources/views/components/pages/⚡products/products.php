<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Product;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Livewire\Attributes\On;

new #[Layout('layouts.default')] #[Title('Lista de Produtos')] class extends Component {
    use WithPagination;

    public string $search = '';
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[On('product-saved')]
    #[Computed]
    public function products()
    {
        return Product::query()
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('id', 'like', '%' . $this->search . '%');
                });
            })
            ->orderByDesc('id')
            ->paginate(8);
    }

    public function delete(Product $product): void
    {
        Gate::authorize('delete', $product);

        $product->forceDelete();
    }

    public function toggleActive(Product $product): void
    {
        Gate::authorize('update', $product);

        $product->update([
            'active' => !$product->active
        ]);
    }
};
