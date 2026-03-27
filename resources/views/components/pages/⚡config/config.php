<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Rules\CnpjValidation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

new #[Layout('layouts.default')] #[Title('Configurações da empresa')] class extends Component {
    public ?string $name = null;
    public ?string $number = null;
    public ?string $address = null;
    public ?string $cnpj = null;

    public function mount()
    {
        $enterprise = Auth::user()->enterprise;

        if ($enterprise) {
            $this->name = $enterprise->name;
            $this->number = $enterprise->number;
            $this->address = $enterprise->address;
            $this->cnpj = $enterprise->cnpj;
        }
    }

    public function rules(): array
    {
        $enterpriseId = Auth::user()->enterprise_id;

        return [
            'name' => [
                'required',
                'min:5',
                'string',
                'max:255',
                Rule::unique('enterprises', 'name')->ignore($enterpriseId),
            ],
            'number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'cnpj' => ['nullable', 'string', 'max:18', new CnpjValidation()],
        ];
    }

    public function messages(): array
    {
        return [
            // Mensagens para o campo 'name'
            'name.required' => 'O nome da empresa é obrigatório.',
            'name.min' => 'O nome tem que ter no minimo 5 caracteres',
            'name.string' => 'O nome da empresa deve ser um texto válido.',
            'name.max' => 'O nome da empresa não pode ultrapassar 255 caracteres.',
            'name.unique' => 'Este nome de empresa já está registrado. Por favor, escolha outro.',

            // Mensagens para o campo 'number'
            'number.string' => 'O número deve ser um formato de texto válido.',
            'number.max' => 'O número não pode ter mais de 20 caracteres.',

            // Mensagens para o campo 'address'
            'address.string' => 'O endereço deve ser um texto válido.',
            'address.max' => 'O endereço não pode ultrapassar 255 caracteres.',

            // Mensagens para o campo 'cnpj'
            'cnpj.string' => 'O CNPJ deve ser um texto válido.',
            'cnpj.max' => 'O CNPJ não pode ultrapassar 18 caracteres.',
        ];
    }
    public function save(): void
    {
        $this->validate();

        $enterprise = Auth::user()->enterprise;

        if ($enterprise) {
            Gate::authorize('update', $enterprise);

            $numberLimpo = preg_replace('/\D/', '', $this->number);
            $cnpjLimpo = preg_replace('/\D/', '', $this->cnpj);

            $enterprise->update([
                'name' => $this->name,
                'number'  => $numberLimpo,
                'address' => $this->address,
                'cnpj'    => $cnpjLimpo,
            ]);

            $this->dispatch('notify', title: 'Sucesso!', message: 'Empresa atualizada com sucesso!', type: 'success');
            return;
        }

        $this->dispatch('notify', title: 'Erro!', message: 'Nenhuma empresa encontrada!', type: 'error');
    }
};
