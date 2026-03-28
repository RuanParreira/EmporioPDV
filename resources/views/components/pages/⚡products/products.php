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
            ->with('category')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%');
                });
            })
            ->orderByDesc('id')
            ->paginate(9);
    }

    public function delete(Product $product): void
    {
        Gate::authorize('delete', $product);

        $product->delete();
        $this->dispatch('notify', title: 'Sucesso!', message: 'Produto deletado com sucesso!', type: 'success');
    }

    public function toggleActive(Product $product): void
    {
        Gate::authorize('update', $product);

        $product->update([
            'is_active' => !$product->is_active
        ]);

        $acao = $product->is_active ? 'ativado' : 'desativado';

        $this->dispatch(
            'notify',
            title: 'Sucesso!',
            message: "Produto {$acao} com sucesso!",
            type: 'success'
        );
    }
};
