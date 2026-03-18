<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

new class extends Component {
    public bool $showModal = false;

    public ?int $userId = null;
    public bool $changePassword = false;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'caixa';

    // Função de validações
    public function rules(): array
    {
        $rules = [
            'name' => 'required|min:3',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->userId)],
            'password' => !$this->userId || $this->changePassword ? 'required|min:8' : 'nullable',
        ];

        // Buscar o cargo original do usuário no banco de dados
        $existingRole = $this->userId ? User::find($this->userId)?->role : null;

        if ($existingRole === 'owner') {
            $rules['role'] = 'required|in:owner';
        } else {
            $rules['role'] = 'required|in:admin,caixa';
        }

        return $rules;
    }

    // Mensagem de erros
    public function messages(): array
    {
        return [
            // Mensagens para o Nome
            'name.required' => 'Nome Obrigatório',
            'name.min' => 'O nome deve ter no mínimo 3 caracteres',

            // Mensagens para o email
            'email.required' => 'O email é obrigatório',
            'email.email' => 'Insira um email válido',
            'email.unique' => 'Esse email já está em uso',

            // Mensagens para a senha
            'password.required' => 'A senha é obrigatória',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres',

            // Mensagem para o cargo
            'role.in' => 'Cargo inválido',
        ];
    }

    #[On('open-user-modal')]
    public function openModal(?int $id = null): void
    {
        $this->reset();
        $this->role = 'caixa';
        $this->resetValidation();

        if ($id) {
            // Fluxo de Edição
            $user = User::findOrFail($id);
            Gate::authorize('update', $user);

            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role = $user->role;
        } else {
            // Fluxo de Criação
            Gate::authorize('create', User::class);
        }

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->userId) {
            // Fluxo de Edição
            $user = User::findOrFail($this->userId);
            Gate::authorize('update', $user);

            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
            ];
            if ($this->changePassword && !empty($this->password)) {
                $data['password'] = Hash::make($this->password);
            }
            $user->update($data);

            session()->flash('success', 'Usuário atualizado com sucesso!');
        } else {
            // Fluxo de Criação
            Gate::authorize('create', User::class);

            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => $this->role,
            ]);
            session()->flash('success', 'Usuário criado com sucesso!');
        }

        $this->showModal = false;
        $this->dispatch('user-saved');
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
                <i class="bi {{ $userId ? 'bi-person-gear' : 'bi-person-plus' }} text-primary"></i>
                {{ $userId ? 'Editar Usuário' : 'Novo Usuário' }}
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
                    <input id="name" type="text" wire:model.blur="name"
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
                    @if ($userId)
                        {{-- Exibe o botão de Alterar a Senha se estiver editando --}}
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-700">Senha do usuário</label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.live="changePassword" class="sr-only peer">
                                <div
                                    class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:inset-s-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary">
                                </div>
                                <span class="ms-2 text-xs font-medium text-gray-600">Alterar senha</span>
                            </label>
                        </div>
                    @else
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                    @endif

                    {{-- Exibe o input sempre se for criação, ou apenas se o toggle estiver ligado na edição --}}
                    @if (!$userId || $changePassword)
                        <input id="password" type="password" wire:model="password"
                            class="flex w-full border border-border bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-gray-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm rounded-lg h-11"
                            placeholder="Digite a {{ $userId ? 'nova ' : '' }}senha">
                        @error('password')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    @endif
                </div>

                {{-- Campo Cargo (Radio Buttons Customizados) --}}
                @if ($role !== 'owner')
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
                @endif
            </div>
            {{-- Botão de Submeter Único e Largo --}}
            <div class="mt-8">
                <button type="submit" wire:loading.attr="disabled" wire:target="save"
                    class="w-full px-4 py-3.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-transform active:scale-[0.98] cursor-pointer flex justify-center items-center">
                    <span wire:loading.remove
                        wire:target="save">{{ $userId ? 'Salvar Alterações' : 'Criar Usuário' }}</span>
                    <span wire:loading wire:target="save">Salvando...</span>
                </button>
            </div>
        </form>
    </div>
</div>
