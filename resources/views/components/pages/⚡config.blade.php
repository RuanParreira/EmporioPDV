<?php

use Livewire\Component;

new class extends Component {
    public function render()
    {
        return $this->view()->layout('layouts.default', [
            'title' => 'Configuração',
        ]);
    }
};
?>

<div class="space-y-4">
    {{-- Titulo --}}
    <div class="flex flex-col lg:flex-row justify-between">
        <x-titulo titulo="Configuração" descricao="Gerencie os usuários do sistema" />
    </div>
</div>
