<?php

use Livewire\Component;

new class extends Component {
    public function render()
    {
        return $this->view()->layout('layouts.default', [
            'title' => 'Histórico de Vendas',
        ]);
    }
};
?>

<div class="space-y-4">
    {{-- Titulo --}}
    <div class="flex flex-col lg:flex-row justify-between">
        <x-titulo titulo="Histórico de Vendas" descricao="Consulte todas as vendas realizadas" />
    </div>
</div>
