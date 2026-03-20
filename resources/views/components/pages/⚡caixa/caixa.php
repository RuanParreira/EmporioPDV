<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;



new #[Layout('layouts.default')] #[Title('Caixa')] class extends Component {
    use WithPagination;
    public string $search = '';
    public ?int $filter = null;



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
};
