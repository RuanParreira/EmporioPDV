<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;

new #[Layout('layouts.default')] #[Title('Lista de Usuários')] class extends Component {
    public Collection $users;

    public bool $showModal = false;
    #[Validate('required|min:3', message: 'O nome deve ter no mínimo 3 caracteres.')]
    public string $name = '';

    #[Validate('required|email|unique:users,email', message: 'Insira um email válido e que não esteja em uso.')]
    public string $email = '';

    #[Validate('required|min:8', message: 'A senha deve ter no mínimo 8 caracteres.')]
    public string $password = '';

    #[Validate('required|in:admin,caixa', message: 'Cargo inválido.')]
    public string $role = 'caixa'; // Valor padrão

    // Carregar os User
    private function loadUsers(): void
    {
        $this->users = User::query()->orderBy('id')->get();
    }

    public function mount(): void
    {
        $this->loadUsers();
    }

    public function create(): void
    {
        $this->resetValidation();
        $this->reset(['name', 'email', 'password']);
        $this->role = 'caixa';
        $this->showModal = true;
    }

    public function save()
    {
        // 1. Validações de segurança e integridade
        $this->authorize('create', User::class);
        $this->validate();

        // 2. Cria o usuário no banco de dados
        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password), // Criptografa a senha!
            'role' => $this->role,
        ]);

        // 3. Redireciona de volta para a lista de usuários
        // Substitua '/usuarios' pela rota correta da sua lista
        return $this->redirect('/users', navigate: true);
    }


    // Função para deletar
    public function delete(User $user): void
    {
        Gate::authorize('delete', $user);

        // Evita apagar o próprio usuário logado
        if (Auth::id() === $user->id) {
            $this->addError('delete', 'Você não pode deletar seu próprio usuário.');
            return;
        }

        $user->delete();

        $this->loadUsers();
    }
};
