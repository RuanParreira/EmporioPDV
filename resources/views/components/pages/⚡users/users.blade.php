<div class="space-y-4">
    {{-- Titulo --}}
    <div class="flex flex-col lg:flex-row justify-between">
        <x-titulo titulo="Usuários" descricao="Gerencie os usuários do sistema" />
        @can('create', \App\Models\User::class)
            <button wire:click="create" class="bg-primary hover:bg-primary/90 h-10 px-4 rounded-lg cursor-pointer">
                <span class="text-white">
                    + Novo Usuário
                </span>
            </button>
        @endcan
    </div>

    @error('delete')
        <span class="text-red-500">{{ $message }}</span>
    @enderror

    {{-- Tabela de Usuarios --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-border bg-primary/80">
                    <th class="text-left text-white p-4 text-xs font-bold uppercase tracking-wider">
                        Nome
                    </th>
                    <th class="text-left text-white p-4 text-xs font-bold uppercase tracking-wider">
                        Email
                    </th>
                    <th class="text-left text-white p-4 text-xs font-bold uppercase tracking-wider">
                        Cargo
                    </th>
                    <th class="text-right text-white p-4 text-xs font-bold uppercase tracking-wider">
                        Ações
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="border-b border-border/50 hover:bg-description/10 transition-colors">
                        <td class="p-4 font-semibold align-middle">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-people text-purple-800"></i>
                                <span class="capitalize">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="p-4 align-middle">
                            <span>{{ $user->email }}</span>
                        </td>
                        <td class="p-4 align-middle">
                            @php
                                [$badgeClass, $iconClass] = match ($user->role) {
                                    'owner' => ['bg-purple-500/10 text-purple-600', 'bi bi-stars'],
                                    'admin' => ['bg-blue-500/10 text-blue-600', 'bi bi-shield-check'],
                                    default => ['bg-yellow-500/10 text-yellow-600', 'bi bi-shop-window'],
                                };
                            @endphp

                            <span
                                class="inline-flex items-center gap-1 text-xs font-bold px-2 py-1 rounded-lg {{ $badgeClass }}">
                                <i class="{{ $iconClass }}"></i>
                                <span class="capitalize">{{ $user->role }}</span>
                            </span>
                        </td>
                        <td class="p-4 text-right space-x-2 align-middle">
                            @can('update', $user)
                                <button
                                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-primary/20 hover:text-purple-950 h-10 w-10 rounded-lg hover:cursor-pointer">
                                    <i class="bi bi-pen text-md"></i>
                                </button>
                            @endcan
                            @can('delete', $user)
                                <button wire:click="delete({{ $user->id }})"
                                    wire:confirm.prompt="Você tem certeza?\n\nDigite DELETAR para confirmar|DELETAR"
                                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50  hover:bg-primary/20 h-10 w-10 rounded-lg text-red-500 hover:text-red-700 cursor-pointer">
                                    <i class="bi bi-trash3 text-md"></i>
                                </button>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Modal --}}
    <div x-data="{ open: @entangle('showModal') }" x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;">

        {{-- Overlay (Fundo Escuro) com Fade --}}
        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-50"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="open = false"
            class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

        {{-- Cartão do Modal com Scale e Fade (Visual Atualizado) --}}
        <div x-show="open" x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-50"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-6 relative z-10 border border-gray-100">

            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">
                    Novo Usuário
                </h3>
                <button @click="open = false"
                    class="text-gray-400 hover:text-gray-700 cursor-pointer transition-colors">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form wire:submit.prevent="save" autocomplete="off">
                <div class="space-y-5">

                    {{-- Campo Nome --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                        {{-- Borda com a cor primary no focus simulando a seleção da imagem --}}
                        <input id="name" type="name" wire:model="name"
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
                                    class="flex items-center justify-center gap-2 py-3 rounded-xl text-sm font-semibold transition-all bg-gray-100 text-gray-500 hover:bg-gray-200 peer-checked:bg-primary peer-checked:text-white border border-transparent peer-checked:border-primary">
                                    <i class="bi bi-shop-window"></i>
                                    Caixa
                                </div>
                            </label>

                            {{-- Opção Administrador --}}
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" wire:model="role" value="admin" class="peer sr-only">
                                <div
                                    class="flex items-center justify-center gap-2 py-3 rounded-xl text-sm font-semibold transition-all bg-gray-100 text-gray-500 hover:bg-gray-200 peer-checked:bg-primary peer-checked:text-white border border-transparent peer-checked:border-primary">
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
                    <button type="submit" wire:loading.attr="disabled"
                        class="w-full px-4 py-3.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-transform active:scale-[0.98] cursor-pointer flex justify-center items-center">
                        <span wire:loading.remove>Criar Usuário</span>
                        <span wire:loading>Criando...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
