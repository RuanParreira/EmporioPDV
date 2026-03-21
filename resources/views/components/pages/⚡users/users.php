<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\WithPagination;
use Livewire\Attributes\On;

new #[Layout('layouts.default')] #[Title('Lista de Usuários')] class extends Component {
    use WithPagination;

    // Listar os users
    #[On('user-saved')]
    #[Computed]
    public function users()
    {
        return User::query()->where('role', '!=', 'dev')->orderByDesc('id')->paginate(9);
    }

    // Deletar um user
    public function delete(User $user): void
    {
        Gate::authorize('delete', $user);

        if (Auth::id() === $user->id) {
            $this->dispatch('notify', title: 'Erro!', message: 'Você não pode deletar seu próprio usuário.', type: 'error');
            return;
        }

        $user->delete();
    }
};
