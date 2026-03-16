<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

new class extends Component {
    public bool $showModal = false;

    #[Validate('required', message: 'Nome Obrigatório')]
    #[Validate('min:3', message: 'O nome deve ter no mínimo 3 caracteres')]
    public string $name = '';

    #[Validate('required', message: 'O email é obrigatório')]
    #[Validate('email', message: 'Insira um email válido')]
    #[Validate('unique:users,email', message: 'Esse email já está em uso')]
    public string $email = '';

    #[Validate('required', message: 'A senha é obrigatória')]
    #[Validate('min:8', message: 'A senha deve ter no mínimo 8 caracteres')]
    public string $password = '';

    #[Validate('required', message: 'O cargo é obrigatório')]
    #[Validate('in:admin,caixa', message: 'Cargo inválido, escolha entre admin e caixa')]
    public string $role = 'caixa'; // Valor padrão

    #[On('open-user-modal')]
    public function openModal(): void
    {
        Gate::authorize('create', User::class);
        $this->reset(['name', 'email', 'password']);
        $this->role = 'caixa';
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save()
    {
        // Verifica a autorização e as validações
        Gate::authorize('create', User::class);
        $this->validate();

        // Cria o usuario
        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
        ]);

        $this->showModal = false;

        $this->dispatch('user-saved');
        session()->flash('success', 'Usuário criado com sucesso!');
    }
};
?>

{{-- Modal --}}
<div x-data="{ open: @entangle('showModal') }" @open-user-modal.window="open = true" x-show="open"
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
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-6 relative z-10">

        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-900">
                Novo Usuário
            </h3>
            <button @click="open = false" class="text-gray-400 hover:text-gray-700 cursor-pointer transition-colors">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form wire:submit.prevent="save" autocomplete="off">
            <div class="space-y-5">

                {{-- Campo Nome --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                    {{-- Borda com a cor primary no focus simulando a seleção da imagem --}}
                    <input id="name" type="text" wire:model="name"
                        class="flex w-full border bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-gray-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm rounded-lg h-11 border-border"
                        placeholder="Digite o nome">
                    @error('name')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
                {{-- Campo Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    {{-- Borda com a cor primary no focus simulando a seleção da imagem --}}
                    <input id="email" type="email" wire:model="email"
                        class="flex w-full border bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-gray-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm rounded-lg h-11 border-border"
                        placeholder="Digite o email">
                    @error('email')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Campo Senha --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                    <input id="password" type="password" wire:model="password"
                        class="flex w-full border border-border bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-gray-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm rounded-lg h-11"
                        placeholder="Digite a senha">
                    @error('password')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Campo Cargo (Radio Buttons Customizados) --}}
                <div>
                    <label class="block text-sm font-medium text-description mb-2">Cargo</label>
                    <div class="flex gap-3">
                        {{-- Opção Caixa --}}
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" wire:model="role" value="caixa" class="peer sr-only">
                            <div
                                class="flex items-center justify-center gap-2 py-3 rounded-xl text-sm font-semibold transition-all bg-gray-100 text-gray-500 hover:bg-primary/20 peer-checked:bg-primary peer-checked:text-white hover:peer-checked:bg-primary/90 border border-transparent peer-checked:border-primary">
                                <i class="bi bi-shop-window"></i>
                                Caixa
                            </div>
                        </label>

                        {{-- Opção Administrador --}}
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" wire:model="role" value="admin" class="peer sr-only">
                            <div
                                class="flex items-center justify-center gap-2 py-3 rounded-xl text-sm font-semibold transition-all bg-gray-100 text-gray-500 hover:bg-primary/20 peer-checked:bg-primary peer-checked:text-white hover:peer-checked:bg-primary/90 border border-transparent peer-checked:border-primary">
                                <i class="bi bi-shield-check"></i>
                                Administrador
                            </div>
                        </label>
                    </div>
                    @error('role')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Botão de Submeter Único e Largo --}}
            <div class="mt-8">
                <button type="submit" wire:loading.attr="disabled" wire:target="save"
                    class="w-full px-4 py-3.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-transform active:scale-[0.98] cursor-pointer flex justify-center items-center">
                    <span wire:loading.remove wire:target="save">Criar Usuário</span>
                    <span wire:loading wire:target="save">Criando...</span>
                </button>
            </div>
        </form>
    </div>
</div>
