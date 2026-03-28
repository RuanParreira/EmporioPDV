<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Rules\CnpjValidation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;

new #[Layout('layouts.default')] #[Title('Configurações da empresa')] class extends Component {
    use WithFileUploads;

    public string $name = '';
    public string $number = '';
    public string $address = '';
    public string $cnpj = '';

    public $logo;
    public ?string $currentLogo = null;
    public bool $isEditing = false;

    public function mount()
    {
        $enterprise = Auth::user()->enterprise;

        if ($enterprise) {
            $this->name = $enterprise->name;
            $this->number = $enterprise->number;
            $this->address = $enterprise->address;
            $this->cnpj = $enterprise->cnpj;
            $this->currentLogo = $enterprise->logo;
        }
    }

    public function toggleEdit(): void
    {
        $this->isEditing = !$this->isEditing;

        if (!$this->isEditing) {
            $this->resetErrorBag();
            $this->mount();
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
            'cnpj' => [
                'required',
                'string',
                'max:18',
                new CnpjValidation,
                Rule::unique('enterprises', 'cnpj')->ignore($enterpriseId),
            ],
            'number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
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
            'number.required' => 'Número Obrigatório',
            'number.string' => 'O número deve ser um formato de texto válido.',
            'number.max' => 'O número não pode ter mais de 20 caracteres.',

            // Mensagens para o campo 'address'
            'address.required' => 'Endereço Obrigatório',
            'address.string' => 'O endereço deve ser um texto válido.',
            'address.max' => 'O endereço não pode ultrapassar 255 caracteres.',

            // Mensagens para o campo 'cnpj'
            'cnpj.required' => 'Cnpj Obrigatório',
            'cnpj.unique' => 'Este CNPJ já está cadastrado para outra empresa.',
            'cnpj.string' => 'O CNPJ deve ser um texto válido.',
            'cnpj.max' => 'O CNPJ não pode ultrapassar 18 caracteres.',

            //Mensagens para o campo 'image'
            'logo.image' => 'Imagem invalida',
            'logo.mimes' => 'Formato de imagem não suportado',
            'logo.max' => 'A imagem é muito pesada',
        ];
    }

    //Salvar informações da empresa
    public function save(): void
    {
        $this->cnpj = preg_replace('/\D/', '', $this->cnpj);
        $this->number = preg_replace('/\D/', '', $this->number);

        $this->validate();

        $enterprise = Auth::user()->enterprise;

        if ($enterprise) {
            Gate::authorize('update', $enterprise);

            $enterprise->update([
                'name' => $this->name,
                'number' => $this->number,
                'address' => $this->address,
                'cnpj' => $this->cnpj,
            ]);

            $this->isEditing = false;

            $this->dispatch('notify', title: 'Sucesso!', message: 'Empresa atualizada com sucesso!', type: 'success');
            $this->dispatch('enterprise-updated');
            return;
        }

        $this->dispatch('notify', title: 'Erro!', message: 'Nenhuma empresa encontrada!', type: 'error');
    }

    // Salvar a logo
    public function saveLogo(): void
    {
        $this->validate([
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $enterprise = Auth::user()->enterprise;

        if ($enterprise && $this->logo) {
            Gate::authorize('update', $enterprise);

            // Deleta a antiga se existir
            if ($enterprise->logo) {
                Storage::disk('public')->delete($enterprise->logo);
            }

            // Salva a nova
            $path = $this->logo->store('logos', 'public');
            $enterprise->update(['logo' => $path]);

            $this->currentLogo = $path;
            $this->logo = null;

            $this->dispatch('notify', title: 'Sucesso!', message: 'Logo atualizada com sucesso!', type: 'success');
            $this->dispatch('enterprise-updated');
        }
    }

    // Deletar a logo
    public function deleteLogo(): void
    {
        $enterprise = Auth::user()->enterprise;

        if ($enterprise && $enterprise->logo) {
            Storage::disk('public')->delete($enterprise->logo);

            $enterprise->update(['logo' => null]);

            $this->currentLogo = null;
            $this->logo = null;

            $this->dispatch('notify', title: 'Sucesso!', message: 'Logo removida com sucesso!', type: 'success');
            $this->dispatch('enterprise-updated');
        } else {
            $this->logo = null;
            $this->resetErrorBag();
        }
    }
};
