<?php

use App\Model\User;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

new #[Title('EmporioCaixa')] class extends Component {
    public string $email = '';
    public string $password = '';

    protected function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|min:6|max:16',
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required' => 'Email Obrigatório!',
            'email.email' => 'Email inválido',
            'password.required' => 'Senha Obrigatória!',
            'password.min' => 'Deve haver no mínimo :min caracteres',
            'password.max' => 'Deve haver no máximo :max caracteres',
        ];
    }

    public function login()
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            RateLimiter::clear($this->throttleKey());
            session()->regenerate();

            return redirect()->intended('dashboard')->with('success', 'Logado com sucesso');
        }

        RateLimiter::hit($this->throttleKey());
        $this->dispatch('notify', title: 'Erro!', message: 'Email ou senha invalidos!', type: 'error');
        $this->password = '';
    }

    protected function ensureIsNotRateLimited()
    {
        //Limitar até 5 tentativas
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'credentials' => "Muitas tentativas de login. Tente novamente em {$seconds} segundos.",
        ]);
    }

    protected function throttleKey()
    {
        // Gera uma chave única usando o email em minúsculas e o IP do usuário
        return strtolower($this->email) . '|' . request()->ip();
    }
};
?>

<div class="min-h-screen flex items-center justify-center bg-background p-4">
    <div class="w-full max-w-lg">
        <div class="bg-white rounded-2xl shadow-lg p-8 space-y-6">
            <div class="flex flex-col items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Logo da Empresa" class="w-20 h-20">
                <div class="text-center">
                    <h1 class="text-2xl font-extrabold text-primary">
                        Empório do Açaí
                        <p class="text-sm text-description font-normal">
                            Sistema de Ponto de Vendas
                        </p>
                    </h1>
                </div>
            </div>
            @error('credentials')
                <span class="flex items-center justify-center text-red-700 text-center">{{ $message }}</span>
            @enderror
            <form wire:submit="login" class="space-y-4">
                <div class="space-y-2">
                    <label for="email" class="text-sm font-semibold text-primary">
                        Email
                    </label>
                    <div class="relative mt-1">
                        <i class="bi bi-envelope absolute left-3 top-1/2 -translate-y-1/2 text-primary"></i>
                        <input type="email" id="email" name="email" wire:model="email"
                            class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#4A195C] focus:border-transparent outline-none transition"
                            placeholder="Digite seu email">
                    </div>
                    @error('email')
                        <span class="text-red-700 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div class="space-y-2">
                    <label for="password" class="text-sm font-semibold text-primary">
                        Senha
                    </label>
                    <div class="relative mt-1">
                        <i class="bi bi-lock absolute left-3 top-1/2 -translate-y-1/2 text-primary"></i>
                        <input type="password" id="password" name="password" wire:model="password"
                            class="w-full px-4 py-3 pl-10  border border-gray-300 rounded-md focus:ring-2 focus:ring-[#4A195C] focus:border-transparent outline-none transition"
                            placeholder="Digite sua senha">
                    </div>
                    @error('password')
                        <span class="text-red-700 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit"
                    class="cursor-pointer inline-flex items-center justify-center whitespace-nowrap ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary hover:bg-primary/90 px-4 py-2 w-full h-12 rounded-xl text-base font-bold gap-2 text-white">
                    <i class="bi bi-box-arrow-right text-lg" wire:loading.remove wire:target="login"></i>
                    <span class="text-lg" wire:loading.remove wire:target="login">
                        Entrar
                    </span>
                    <span wire:loading wire:target="login" class="text-lg">Entrando...</span>
                </button>
            </form>
        </div>
    </div>
</div>
