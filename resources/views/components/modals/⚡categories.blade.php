<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Category;
use Illuminate\Support\Facades\Gate;

new class extends Component {
    public bool $showModal = false;
    public string $name = '';
    public ?int $categoryId = null;

    // Escuta o evento 'open-category-modal' para abrir o modal
    #[On('open-category-modal')]
    public function openModal(?int $id = null): void
    {
        $this->reset();
        $this->resetValidation();

        if ($id) {
            $category = Category::findOrFail($id);
            Gate::authorize('update', $category);
            $this->categoryId = $category->id;
            $this->name = $category->name;
        } else {
            Gate::authorize('create', Category::class);
        }

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate(
            [
                'name' => 'required|min:3|unique:categories,name,' . $this->categoryId,
            ],
            [
                'name.required' => 'Nome Obrigatório',
                'name.min' => 'Precisa ter no minimo 3 caracter',
                'name.unique' => 'Essa categoria já existe',
            ],
        );

        if ($this->categoryId) {
            $category = Category::findOrFail($this->categoryId);
            Gate::authorize('update', $category);
            $category->update(['name' => $this->name]);

            $this->dispatch('notify', title: 'Sucesso!', message: 'Categoria atualizada com sucesso!', type: 'success');
        } else {
            Gate::authorize('create', Category::class);
            Category::create(['name' => $this->name]);

            $this->dispatch('notify', title: 'Sucesso!', message: 'Categoria criada com sucesso!', type: 'success');
        }

        $this->showModal = false;

        // Avisa a página de categorias que uma nova foi salva para atualizar a lista
        $this->dispatch('category-saved');
    }
};
?>


<div x-data="{ open: @entangle('showModal') }" x-show="open" @keydown.escape.window="open = false"
    class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
    {{-- Overlay (Fundo Escuro) --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-50"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="open = false"
        class="absolute inset-0 bg-black/80"></div>

    {{-- Cartão do Modal --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-50"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4" class="modal max-w-sm">

        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-bold">
                <i class="bi bi-bag-plus text-primary"></i> Nova Categoria
            </h3>
            <button type="button" @click="open = false" class="cursor-pointer text-gray-400 hover:text-gray-600">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form wire:submit="save" autocomplete="off">
            <div class="space-y-4">
                <div>
                    <label for="name" class="mb-1 block text-sm font-medium text-gray-700">Nome da
                        Categoria</label>
                    <input id="name" type="text" wire:model="name" placeholder="Digite o nome da categoria"
                        class="input-modal">
                    @error('name')
                        <span class="mt-1 text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex gap-3">
                    <button type="submit" wire:loading.attr"disable" wire:target="save" class="modal-button">
                        <span wire:loading.remove wire:target="save">
                            Salvar
                        </span>
                        <span wire:loading wire:target="save">
                            Salvando...
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
