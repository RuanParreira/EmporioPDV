<?php

use App\Models\Sale;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

new #[Layout('layouts.default')] #[Title('Histórico de Vendas')] class extends Component {
    use WithPagination;

    #[Computed]
    public function sales()
    {
        return Sale::with(['items', 'user'])->latest()->paginate(20);
    }

    public function delete(Sale $sale)
    {
        Gate::authorize('delete', $sale);

        $sale->delete();
        $this->dispatch('notify', title: 'Sucesso!', message: 'Venda deletada com sucesso!', type: 'success');
    }
};
