<?php

use Livewire\Component;

new class extends Component {
    public function render()
    {
        return $this->view()->layout('layouts.default', [
            'title' => 'Produtos',
        ]);
    }
};
?>

<div class="space-y-4">
    {{-- Titulo --}}
    <div class="flex flex-col lg:flex-row justify-between">
        <x-titulo titulo="Produtos" descricao="Gerencie os produtos do sistema" />
        {{-- @can('create', \App\Models\Pro::class) --}}
        <button wire:click="create" class="bg-primary hover:bg-primary/90 h-10 px-4 rounded-lg cursor-pointer">
            <span class="text-white">
                + Novo Produto
            </span>
        </button>
        {{-- @endcan --}}
    </div>
</div>
