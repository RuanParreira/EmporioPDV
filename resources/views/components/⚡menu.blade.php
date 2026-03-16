<?php

use Livewire\Component;
use Illuminate\Support;

new class extends Component {
    public $user;

    public function mount(): void
    {
        $this->user = Auth::user();
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

<div class=" min-h-screen bg-slate-100 fixed top-0 left-0 hidden lg:flex">
    <aside class="bg-white shadow-sm border border-slate-100 flex flex-col font-sans w-[16rem]">
        <div class="p-6 flex items-center gap-4">
            <div
                class="w-11 h-11 rounded-full bg-[#4e1c53] text-white flex items-center justify-center shrink-0 shadow-sm">
                <img src="{{ asset('images/logo.png') }}" alt="Logo da Empressa">
            </div>
            <div class="flex flex-col">
                <span class="text-[#4e1c53] font-bold text-base leading-tight">Empório do Açaí</span>
                <span class="text-slate-400 text-xs font-medium">Painel de Navegação</span>
            </div>
        </div>

        <nav class="flex-1 px-2 py-2 space-y-4">

            <a href="/dashboard" wire:navigate @class([
                'item-menu',
                'item-menu-active' => request()->routeIs('dashboard'),
            ])>
                <i class="bi bi-grid text-xl"></i>
                <span class="font-medium text-sm">Dashboard</span>
            </a>

            <a href="/caixa" wire:navigate @class([
                'item-menu',
                'item-menu-active' => request()->routeIs('caixa'),
            ])>
                <i class="bi bi-cart text-xl"></i>
                <span class="font-medium text-sm">Caixa</span>
            </a>


            <a href="/products" wire:navigate @class([
                'item-menu',
                'item-menu-active' => request()->routeIs('prducts'),
            ])>
                <i class="bi bi-archive text-xl"></i>
                <span class="font-medium text-sm">Produtos</span>
            </a>

            <a href="/categories" wire:navigate @class([
                'item-menu',
                'item-menu-active' => request()->routeIs('categories'),
            ])>
                <i class="bi bi-tags text-xl"></i>
                <span class="font-medium text-sm">Categorias</span>
            </a>

            <a href="/users" wire:navigate @class([
                'item-menu',
                'item-menu-active' => request()->routeIs('users'),
            ])>
                <i class="bi bi-people text-xl"></i>
                <span class="font-medium text-sm">Usuários</span>
            </a>

            <a href="/sales" wire:navigate @class([
                'item-menu',
                'item-menu-active' => request()->routeIs('sales'),
            ])>
                <i class="bi bi-wallet text-xl"></i>
                <span class="font-medium text-sm">Vendas</span>
            </a>

            {{-- Configurações --}}
            <hr class="my-3 border-slate-200 mx-3">

            <a href="/config" wire:navigate @class([
                'item-menu',
                'item-menu-active' => request()->routeIs('config'),
            ])>
                <i class="bi bi-gear text-xl"></i>
                <span class="font-medium text-sm">Configurações</span>
            </a>

        </nav>

        <div class="mt-auto border-t border-slate-100 p-4">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-full bg-purple-100 text-[#4e1c53] flex items-center justify-center font-bold text-sm shrink-0">
                    {{ Str::of($user?->name ?? 'US')->trim()->substr(0, 2)->upper() }}
                </div>
                <div class="flex flex-col flex-1 overflow-hidden">
                    <span class="text-sm font-semibold text-slate-800 truncate">
                        {{ $user?->name ?? 'Usuário' }}
                    </span>
                    <span class="text-xs text-slate-400 truncate">{{ $user->role }}</span>
                </div>
                <button type="button" wire:click="logout"
                    class="cursor-pointer text-slate-400 hover:text-[#4e1c53] transition-colors p-1">
                    <i class="bi bi-box-arrow-right text-2xl"></i>
                </button>
            </div>
        </div>
    </aside>
</div>
