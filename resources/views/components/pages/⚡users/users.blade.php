<div class="space-y-4 p-6">
    {{-- Titulo --}}
    <div class="flex flex-col justify-between lg:flex-row">
        <x-titulo titulo="Usuários" descricao="Gerencie os usuários do sistema" />
        <button type="button" x-on:click="$dispatch('open-user-modal')"
            class="bg-primary hover:bg-primary/90 h-10 cursor-pointer rounded-lg px-4">
            <span class="text-white">
                + Novo Usuário
            </span>
        </button>
    </div>

    @error('delete')
        <span class="text-red-500">{{ $message }}</span>
    @enderror

    {{-- Tabela de Usuarios --}}
    <div class="overflow-hidden rounded-xl bg-white shadow-md">
        <table class="w-full">
            <thead>
                <tr class="border-border bg-primary border-b">
                    <th class="p-4 text-left text-xs font-bold uppercase tracking-wider text-white">
                        Nome
                    </th>
                    <th class="p-4 text-left text-xs font-bold uppercase tracking-wider text-white">
                        Email
                    </th>
                    <th class="p-4 text-left text-xs font-bold uppercase tracking-wider text-white">
                        Cargo
                    </th>
                    <th class="p-4 text-right text-xs font-bold uppercase tracking-wider text-white">
                        Ações
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->users as $user)
                    <tr class="border-border/50 hover:bg-description/10 border-b transition-colors">
                        <td class="p-4 align-middle font-semibold">
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
                                class="{{ $badgeClass }} inline-flex items-center gap-1 rounded-lg px-2 py-1 text-xs font-bold">
                                <i class="{{ $iconClass }}"></i>
                                <span class="capitalize">{{ $user->role }}</span>
                            </span>
                        </td>
                        <td class="p-4 text-right align-middle">
                            @can('update', $user)
                                <button type="button"
                                    x-on:click="$dispatch('open-user-modal', { id: {{ $user->id }} })"
                                    class="ring-offset-background focus-visible:ring-ring hover:bg-primary/20 inline-flex h-10 w-10 items-center justify-center whitespace-nowrap rounded-lg text-sm font-medium transition-colors hover:cursor-pointer hover:text-purple-950 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50">
                                    <i class="bi bi-pen text-md"></i>
                                </button>
                            @endcan
                            @can('delete', $user)
                                <button type="button" wire:click="delete({{ $user->id }})"
                                    wire:confirm.prompt="Você tem certeza?\n\nDigite DELETAR para confirmar|DELETAR"
                                    class="ring-offset-background focus-visible:ring-ring hover:bg-primary/20 inline-flex h-10 w-10 cursor-pointer items-center justify-center whitespace-nowrap rounded-lg text-sm font-medium text-red-500 transition-colors hover:text-red-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50">
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
