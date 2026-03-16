<?php

use Livewire\Component;

new class extends Component {
    public function render()
    {
        return $this->view()->layout('layouts.default', [
            'title' => 'Caixa',
        ]);
    }
};
?>

<div class="space-y-4">
    {{-- Titulo --}}
    <div class="flex flex-col lg:flex-row justify-between">
        <x-titulo titulo="Nova Venda" />
    </div>
</div>
