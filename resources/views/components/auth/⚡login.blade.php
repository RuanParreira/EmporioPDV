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
    public bool $showPassword = false;

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

<div class="bg-background flex min-h-screen items-center justify-center p-4">
    <div class="w-full max-w-lg">
        <div class="space-y-6 rounded-2xl bg-white p-8 shadow-lg">
            <div class="flex flex-col items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Logo da Empresa" class="h-20 w-20">
                <div class="text-center">
                    <h1 class="text-primary text-2xl font-extrabold">
                        Empório do Açaí
                        <p class="text-description text-sm font-normal">
                            Sistema de Ponto de Vendas
                        </p>
                    </h1>
                </div>
            </div>
            @error('credentials')
                <span class="flex items-center justify-center text-center text-red-700">{{ $message }}</span>
            @enderror
            <form wire:submit="login" class="space-y-4">
                <div class="space-y-2">
                    <label for="email" class="text-primary text-sm font-semibold">
                        Email
                    </label>
                    <div class="relative mt-1">
                        <i class="bi bi-envelope text-primary absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="email" id="email" name="email" wire:model="email"
                            class="w-full rounded-md border border-gray-300 px-4 py-3 pl-10 outline-none transition focus:border-transparent focus:ring-2 focus:ring-[#4A195C]"
                            placeholder="Digite seu email">
                    </div>
                    @error('email')
                        <span class="text-sm text-red-700">{{ $message }}</span>
                    @enderror
                </div>
                <div class="space-y-2">
                    <label for="password" class="text-primary text-sm font-semibold">
                        Senha
                    </label>
                    <div class="relative mt-1">
                        <i class="bi bi-lock text-primary absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="{{ $showPassword ? 'text' : 'password' }}" id="password" name="password"
                            wire:model="password"
                            class="w-full rounded-md border border-gray-300 px-4 py-3 pl-10 pr-10 outline-none transition focus:border-transparent focus:ring-2 focus:ring-[#4A195C]"
                            placeholder="Digite sua senha">

                        {{-- Botão com wire:click para alternar --}}
                        <button type="button" wire:click="$toggle('showPassword')"
                            class="hover:text-primary/60 text-primary absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer items-center justify-center">
                            <i class="bi {{ $showPassword ? 'bi-eye-slash' : 'bi-eye' }} text-lg"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="text-sm text-red-700">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit"
                    class="ring-offset-background focus-visible:ring-ring bg-primary hover:bg-primary/90 inline-flex h-12 w-full cursor-pointer items-center justify-center gap-2 whitespace-nowrap rounded-xl px-4 py-2 text-base font-bold text-white transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50">
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
