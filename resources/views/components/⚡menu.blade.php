<?php

use Livewire\Component;
use Illuminate\Support;

new class extends Component {
    public function User()
    {
        return Auth::user();
    }

    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirect('/', navigate: true);
    }
};
?>

<aside x-data="{ expanded: $persist(true) }" :class="expanded ? 'w-64' : 'w-20'"
    class="relative flex min-h-screen flex-col border border-slate-100 bg-white shadow-sm transition-all duration-300">

    <button @click="expanded = !expanded"
        class="absolute -right-3 top-8 flex h-6 w-6 cursor-pointer items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm transition-colors hover:text-[#4e1c53]">
        <i class="bi text-xs" :class="expanded ? 'bi-chevron-left' : 'bi-chevron-right'"></i>
    </button>

    <div class="flex items-center gap-4 px-4 py-6">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#4e1c53] text-white shadow-sm">
            <img src="{{ asset('images/logo.png') }}" alt="Logo da Empresa" class="object-contain">
        </div>
        <div class="flex flex-col overflow-hidden" x-show="expanded" x-transition>
            <span
                class="whitespace-nowrap text-base font-bold leading-tight text-[#4e1c53]">{{ $this->User()->enterprise->name ?? 'Dev' }}</span>
            <span class="whitespace-nowrap text-xs font-medium text-slate-400">Painel de Navegação</span>
        </div>
    </div>

    <nav class="flex-1 space-y-4 px-2 py-2">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}" wire:navigate @class([
            'item-menu flex items-center gap-3',
            'item-menu-active' => request()->routeIs('dashboard'),
        ])
            :class="expanded ? 'justify-start px-3' : 'justify-center px-0'">
            <i class="bi bi-grid shrink-0 text-xl"></i>
            <span class="whitespace-nowrap text-sm font-medium" x-show="expanded">Dashboard</span>
        </a>

        {{-- Caixa --}}
        <a href="{{ route('caixa') }}" wire:navigate @class([
            'item-menu flex items-center gap-3',
            'item-menu-active' => request()->routeIs('caixa'),
        ])
            :class="expanded ? 'justify-start px-3' : 'justify-center px-0'">
            <i class="bi bi-cart shrink-0 text-xl"></i>
            <span class="whitespace-nowrap text-sm font-medium" x-show="expanded">Caixa</span>
        </a>

        {{-- Produtos --}}
        <a href="{{ route('products') }}" wire:navigate @class([
            'item-menu flex items-center gap-3',
            'item-menu-active' => request()->routeIs('products'),
        ])
            :class="expanded ? 'justify-start px-3' : 'justify-center px-0'">
            <i class="bi bi-archive shrink-0 text-xl"></i>
            <span class="whitespace-nowrap text-sm font-medium" x-show="expanded">Produtos</span>
        </a>

        {{-- Categorias --}}
        @can('viewAny', \App\Models\Category::class)
            <a href="{{ route('categories') }}" wire:navigate @class([
                'item-menu flex items-center gap-3',
                'item-menu-active' => request()->routeIs('categories'),
            ])
                :class="expanded ? 'justify-start px-3' : 'justify-center px-0'">
                <i class="bi bi-tags shrink-0 text-xl"></i>
                <span class="whitespace-nowrap text-sm font-medium" x-show="expanded">Categorias</span>
            </a>
        @endcan

        {{-- Usuarios --}}
        @can('viewAny', \App\Models\User::class)
            <a href="{{ route('users') }}" wire:navigate @class([
                'item-menu flex items-center gap-3',
                'item-menu-active' => request()->routeIs('users'),
            ])
                :class="expanded ? 'justify-start px-3' : 'justify-center px-0'">
                <i class="bi bi-people shrink-0 text-xl"></i>
                <span class="whitespace-nowrap text-sm font-medium" x-show="expanded">Usuários</span>
            </a>
        @endcan

        {{-- Vendas --}}
        <a href="{{ route('sales') }}" wire:navigate @class([
            'item-menu flex items-center gap-3',
            'item-menu-active' => request()->routeIs('sales'),
        ])
            :class="expanded ? 'justify-start px-3' : 'justify-center px-0'">
            <i class="bi bi-wallet shrink-0 text-xl"></i>
            <span class="whitespace-nowrap text-sm font-medium" x-show="expanded">Vendas</span>
        </a>
        {{-- Configuração --}}
        @can('update', $this->User()->enterprise)
            <hr class="mx-3 my-3 border-slate-200">

            <a href="{{ route('config') }}" wire:navigate @class([
                'item-menu flex items-center gap-3',
                'item-menu-active' => request()->routeIs('config'),
            ])
                :class="expanded ? 'justify-start px-3' : 'justify-center px-0'">
                <i class="bi bi-gear shrink-0 text-xl"></i>
                <span class="whitespace-nowrap text-sm font-medium" x-show="expanded">Configurações</span>
            </a>
        @endcan



    </nav>


    <div class="mt-auto border-t border-slate-100 p-4">
        <div class="flex items-center gap-3" :class="expanded ? '' : 'flex-col justify-center gap-4'">
            @if ($this->User())
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-purple-100 text-sm font-bold text-[#4e1c53]">
                    {{ Str::of($this->User()->name)->trim()->substr(0, 2)->upper() }}
                </div>
                <div class="flex flex-1 flex-col overflow-hidden" x-show="expanded" x-transition>
                    <span class="truncate text-sm font-semibold capitalize text-slate-800">
                        {{ $this->User()->name }}
                    </span>
                    <span class="truncate text-xs capitalize text-slate-400">
                        {{ $this->User()->role ?? 'Membro' }}
                    </span>
                </div>
            @endif

            <button type="button" wire:click="logout" wire:confirm="Deseja realmente sair?"
                class="cursor-pointer p-1 text-slate-400 transition-colors hover:text-[#4e1c53]">
                <i class="bi bi-box-arrow-right text-2xl"></i>
            </button>
        </div>
    </div>
</aside>
