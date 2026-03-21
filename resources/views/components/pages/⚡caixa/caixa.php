<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

use function PHPSTORM_META\type;

new #[Layout('layouts.default')] #[Title('Caixa')] class extends Component {
    use WithPagination;
    public string $search = '';
    public ?int $filter = null;
    public string $searchId = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function categories()
    {
        return Category::orderBy('name')->get();
    }

    #[Computed]
    public function products()
    {
        return Product::query()
            ->with('category')
            ->where('active', true)
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->filter !== null, function ($query) {
                $query->where('category_id', $this->filter);
            })
            ->paginate(20);
    }

    public function addSearchedProduct()
    {
        if (!empty($this->searchId)) {
            $product = Product::where('id', $this->searchId)
                ->where('active', true)
                ->first();

            if ($product) {
                if ($product->measure_unit === 'KG') {
                    $this->dispatch('open-weight-modal', id: $product->id, name: $product->name, price: $product->price);
                } else {
                    $this->dispatch('add-to-cart', productId: $product->id);
                }
            }
            // 2. Se NÃO ENCONTROU o produto (Aviso vem para cá)
            else {
                $this->dispatch('notify', title: 'Atenção', type: 'error', message: 'Produto não encontrado ou inativo!');
            }

            // 3. Limpa o input SEMPRE, independente se achou ou não (fora do if/else)
            $this->searchId = '';
        }
    }
};
