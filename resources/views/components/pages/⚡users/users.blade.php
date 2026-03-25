<div class="space-y-4 p-6">
    {{-- Titulo --}}
    <div class="flex flex-col justify-between lg:flex-row">
        <x-titulo titulo="Usuários" descricao="Gerencie os usuários do sistema" />
        <button type="button" x-on:click="$dispatch('open-user-modal')" class="button-new">
            <span>
                + Novo Usuário
            </span>
        </button>
    </div>

    @error('delete')
        <span class="text-red-500">{{ $message }}</span>
    @enderror

    {{-- Tabela de Usuarios --}}
    <div class="table-default">
        <table>
            <thead>
                <tr>
                    <th>
                        Nome
                    </th>
                    <th>
                        Email
                    </th>
                    <th>
                        Cargo
                    </th>
                    <th class="text-right">
                        Ações
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->users as $user)
                    <tr>
                        <td class="font-semibold">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-people text-purple-800"></i>
                                <span class="capitalize">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td>
                            <span>{{ $user->email }}</span>
                        </td>
                        <td>
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
                        <td class="text-right">
                            @can('update', $user)
                                <button type="button"
                                    x-on:click="$dispatch('open-user-modal', { id: {{ $user->id }} })"
                                    class="edit-button">
                                    <i class="bi bi-pen text-md"></i>
                                </button>
                            @endcan
                            @can('delete', $user)
                                <button type="button" wire:click="delete({{ $user->id }})"
                                    wire:confirm.prompt="Você tem certeza?\n\nDigite DELETAR para confirmar|DELETAR"
                                    class="delete-button">
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
