<?php

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

new class extends Component {
    public bool $showModal = false;

    public ?int $productId = null;

    public string $name = '';
    public string $price = '';
    public string $category_id = '';
    public string $measure_unit = 'UN';

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:3', Rule::unique('products', 'name')->ignore($this->productId)],
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'measure_unit' => 'required|in:UN,KG',
        ];
    }

    public function messages(): array
    {
        return [
            // Mensagens para o Nome
            'name.required' => 'O nome do produto é obrigatório.',
            'name.string' => 'O nome deve ser um texto válido.',
            'name.max' => 'O nome não pode ter mais que 255 caracteres.',
            'name.min' => 'O nome deve ter no mínimo 3 caracteres.',
            'name.unique' => 'Já existe um produto com este nome cadastrado.',

            // Mensagens para o Preço
            'price.required' => 'O preço é obrigatório.',
            'price.numeric' => 'O preço deve ser um valor numérico válido.',
            'price.min' => 'O preço não pode ser menor que zero.',

            // Mensagens para a Categoria
            'category_id.required' => 'A categoria é obrigatória.',
            'category_id.exists' => 'A categoria selecionada é inválida ou não existe.',

            // Mensagens para a Unidade de Medida
            'measure_unit.required' => 'O tipo de venda é obrigatório.',
            'measure_unit.in' => 'Selecione um tipo de venda válido (Unidade ou Peso).',
        ];
    }

    #[On('open-product-modal')]
    public function openModal(?int $id = null): void
    {
        $this->reset();
        $this->measure_unit = 'UN';
        $this->resetValidation();

        if ($id) {
            $product = Product::findOrFail($id);
            Gate::authorize('update', $product);

            $this->productId = $product->id;
            $this->name = $product->name;
            $this->category_id = $product->category_id;
            $this->price = $product->price;
            $this->measure_unit = $product->measure_unit;
        } else {
            Gate::authorize('create', Product::class);
        }

        $this->showModal = true;
    }

    public function with(): array
    {
        return [
            'categories' => Category::select('id', 'name')->orderBy('name')->get(),
        ];
    }

    public function save(): void
    {
        $this->validate();

        if ($this->productId) {
            $product = Product::findOrFail($this->productId);
            Gate::authorize('update', $product);

            $product->update([
                'name' => $this->name,
                'price' => (float) $this->price,
                'category_id' => $this->category_id,
                'measure_unit' => $this->measure_unit,
            ]);

            session()->flash('product', 'Produto atualizado com sucesso!');
        } else {
            Gate::authorize('create', Product::class);

            Product::create([
                'name' => $this->name,
                'price' => (float) $this->price,
                'category_id' => $this->category_id,
                'measure_unit' => $this->measure_unit,
                'active' => true,
            ]);

            session()->flash('product', 'Produto criado com sucesso!');
        }

        $this->showModal = false;
        $this->dispatch('product-saved');
    }
};
?>

{{-- Modal --}}
<div x-data="{ open: @entangle('showModal') }" x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display: none;">
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
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-6 relative z-10">

        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-900">
                <i class="bi {{ $productId ? 'bi-pencil-square' : 'bi bi-basket3' }} text-primary"></i>
                {{ $productId ? 'Editar Produto' : 'Novo Produto' }}
            </h3>
            <button @click="open = false" class="text-gray-400 hover:text-gray-700 cursor-pointer transition-colors">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form wire:submit.prevent="save" autocomplete="off">
            <div class="space-y-5">

                {{-- Campo Nome --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome do Produto</label>
                    {{-- Borda com a cor primary no focus simulando a seleção da imagem --}}
                    <input id="name" type="text" wire:model.blur="name"
                        class="flex w-full border bg-input px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-gray-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm rounded-lg h-11 border-border"
                        placeholder="Digite o nome do produto">
                    @error('name')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>


                {{-- Campo Preço --}}
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Preço (R$)</label>
                    {{-- Borda com a cor primary no focus simulando a seleção da imagem --}}
                    <input id="price" type="text" wire:model.blur="price"
                        class="flex w-full border bg-input px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-gray-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm rounded-lg h-11 border-border"
                        placeholder="Digite o preço">
                    @error('price')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Campo Categoria (Dropdown com Busca e Navegação por Teclado) --}}
                <div x-data="{
                    open: false,
                    search: '',
                    selectedId: @entangle('category_id'),
                    categories: {{ Js::from($categories) }},
                    highlightedIndex: 0,
                
                    get filteredCategories() {
                        if (this.search === '') return this.categories;
                        return this.categories.filter(c => c.name.toLowerCase().includes(this.search.toLowerCase()));
                    },
                
                    get selectedName() {
                        if (!this.selectedId) return 'Selecione uma categoria...';
                        let cat = this.categories.find(c => c.id == this.selectedId);
                        return cat ? cat.name : 'Selecione uma categoria...';
                    },
                
                    selectCategory(category) {
                        if (category) {
                            this.selectedId = category.id;
                            this.open = false;
                            this.search = '';
                            this.$nextTick(() => this.$refs.dropdownButton.focus())
                        }
                    },
                
                    init() {
                        // Zera o highlight sempre que a busca mudar
                        this.$watch('search', () => { this.highlightedIndex = 0; });
                    }
                }" class="relative">

                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>

                    {{-- Botão Principal --}}
                    <button x-ref="dropdownButton" type="button"
                        @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus());"
                        @click.away="open = false"
                        @keydown.arrow-down.prevent="open = true; $nextTick(() => $refs.searchInput.focus());"
                        class="flex items-center justify-between w-full px-3 py-2 border bg-input rounded-lg h-11 border-border focus:ring-2 focus:ring-primary focus:outline-none transition-all md:text-sm"
                        :class="{ 'text-gray-900': selectedId, 'text-gray-400': !selectedId }">
                        <span x-text="selectedName" class="truncate"></span>
                        <i class="bi bi-chevron-down text-gray-400 transition-transform duration-200"
                            :class="{ 'rotate-180': open }"></i>
                    </button>

                    {{-- Painel do Dropdown --}}
                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden"
                        style="display: none;">
                        {{-- Input da Busca com Eventos de Teclado --}}
                        <div class="p-2 border-b border-gray-100 bg-gray-50/50">
                            <div class="relative">
                                <i
                                    class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input x-ref="searchInput" type="text" x-model="search"
                                    placeholder="Buscar categoria..."
                                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary bg-white"
                                    @keydown.arrow-down.prevent="highlightedIndex = Math.min(highlightedIndex + 1, filteredCategories.length - 1)"
                                    @keydown.arrow-up.prevent="highlightedIndex = Math.max(highlightedIndex - 1, 0)"
                                    @keydown.enter.prevent="selectCategory(filteredCategories[highlightedIndex])"
                                    @keydown.escape.prevent="open = false; $nextTick(() => $refs.dropdownButton.focus());">
                            </div>
                        </div>

                        {{-- Lista de Categorias --}}
                        <ul class="max-h-48 overflow-y-auto p-1 space-y-0.5" id="category-list">
                            <template x-for="(category, index) in filteredCategories" :key="category.id">
                                <li @click="selectCategory(category)" @mouseenter="highlightedIndex = index"
                                    class="px-3 py-2 text-sm cursor-pointer rounded-lg transition-colors flex items-center justify-between"
                                    :class="{
                                        'bg-primary/10 text-primary font-semibold': selectedId == category.id,
                                        'bg-gray-100 text-gray-900': highlightedIndex === index && selectedId !=
                                            category.id,
                                        'text-gray-700': highlightedIndex !== index && selectedId != category.id
                                    }">
                                    <span x-text="category.name"></span>
                                    <i x-show="selectedId == category.id" class="bi bi-check2 text-lg"></i>
                                </li>
                            </template>

                            <li x-show="filteredCategories.length === 0"
                                class="px-3 py-4 text-sm text-center text-gray-500">
                                Nenhuma categoria encontrada.
                            </li>
                        </ul>
                    </div>

                    @error('category_id')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Selecionar o tipo --}}
                <div>
                    <label class="block text-sm font-medium text-description mb-2">Tipo de Venda</label>
                    <div class="flex gap-3">
                        {{-- Opção Unidade --}}
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" wire:model="measure_unit" value="UN" class="peer sr-only">
                            <div
                                class="flex items-center justify-center gap-2 py-3 rounded-xl text-sm font-semibold transition-all bg-gray-100 text-gray-500 hover:bg-primary/20 peer-checked:bg-primary peer-checked:text-white hover:peer-checked:bg-primary/90 border border-transparent peer-checked:border-primary">
                                <i class="bi bi-box-seam"></i>
                                Por Unidade
                            </div>
                        </label>

                        {{-- Opção Peso --}}
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" wire:model="measure_unit" value="KG" class="peer sr-only">
                            <div
                                class="flex items-center justify-center gap-2 py-3 rounded-xl text-sm font-semibold transition-all bg-gray-100 text-gray-500 hover:bg-primary/20 peer-checked:bg-primary peer-checked:text-white hover:peer-checked:bg-primary/90 border border-transparent peer-checked:border-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-weight-icon lucide-weight">
                                    <circle cx="12" cy="5" r="3" />
                                    <path
                                        d="M6.5 8a2 2 0 0 0-1.905 1.46L2.1 18.5A2 2 0 0 0 4 21h16a2 2 0 0 0 1.925-2.54L19.4 9.5A2 2 0 0 0 17.48 8Z" />
                                </svg>
                                Por Peso
                            </div>
                        </label>
                    </div>
                    @error('measure_unit')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

            </div>
            {{-- Botão de Submeter --}}
            <div class="mt-8">
                <button type="submit" wire:loading.attr="disabled" wire:target="save"
                    class="w-full px-4 py-3.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-transform active:scale-[0.98] cursor-pointer flex justify-center items-center">
                    <span wire:loading.remove wire:target="save">
                        {{ $productId ? 'Atualizar Produto' : 'Criar Produto' }}
                    </span>
                    <span wire:loading wire:target="save">Salvando...</span>
                </button>
            </div>
        </form>
    </div>
</div>
