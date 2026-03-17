<div class="space-y-4">
    {{-- Titulo --}}
    <div class="flex flex-col lg:flex-row justify-between">
        <x-titulo titulo="Usuários" descricao="Gerencie os usuários do sistema" />
        @can('create', \App\Models\User::class)
            <button x-on:click="$dispatch('open-user-modal')"
                class="bg-primary hover:bg-primary/90 h-10 px-4 rounded-lg cursor-pointer">
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
                @foreach ($this->users as $user)
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
                                    'owner' => ['bg-purple-500/10 w-18 text-purple-600', 'bi bi-stars'],
                                    'admin' => ['bg-blue-500/10 w-18 text-blue-600', 'bi bi-shield-check'],
                                    default => ['bg-yellow-500/10 w-18 text-yellow-600', 'bi bi-shop-window'],
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
                                <button x-on:click="$dispatch('open-user-modal', { id: {{ $user->id }} })"
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
    <div class="mt-4">
        {{ $this->users->links() }}
    </div>

    <livewire:modals.users />
</div>
