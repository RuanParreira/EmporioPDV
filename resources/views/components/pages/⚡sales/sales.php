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
