<div class="space-y-4 p-6">
    {{-- Titulo --}}
    <div class="flex flex-col justify-between lg:flex-row">
        <x-titulo titulo="Configuração" descricao="Gerencie as informações da sua empresa" />
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-slate-100 bg-white shadow-sm lg:col-span-1">
            <div class="flex flex-col items-center gap-5 p-8">
                <input type="file" wire:model="logo" id="logoInput" class="hidden" accept="image/*">

                <div class="group relative cursor-pointer" onclick="document.getElementById('logoInput').click()">
                    <div
                        class="border-primary/20 group-hover:border-primary/50 bg-primary/10 flex h-32 w-32 items-center justify-center overflow-hidden rounded-full border-4 transition-all duration-300 group-hover:shadow-lg">
                        @if ($logo)
                            @if (in_array($logo->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'webp', 'gif']))
                                <img src="{{ $logo->temporaryUrl() }}" class="h-full w-full object-cover">
                            @else
                                <div class="flex flex-col items-center justify-center text-red-800/60">
                                    <i class="bi bi-file-earmark-excel text-7xl"></i>
                                </div>
                            @endif
                        @elseif ($currentLogo)
                            <img src="{{ asset('storage/' . $currentLogo) }}" class="h-full w-full object-cover">
                        @else
                            <i class="bi bi-shop text-primary/60 text-7xl"></i>
                        @endif

                        <div wire:loading wire:target="logo"
                            class="absolute inset-0 flex items-center justify-center bg-white/50">
                            <i class="bi bi-arrow-repeat text-primary inline-block animate-spin text-3xl"></i>
                        </div>
                    </div>
                    <div
                        class="bg-primary/0 group-hover:bg-primary/40 absolute inset-0 flex items-center justify-center rounded-full transition-all duration-300">
                        <i
                            class="bi bi-camera text-2xl text-white opacity-0 transition-opacity duration-300 group-hover:opacity-100"></i>
                    </div>
                </div>

                <div class="text-center">
                    <p class="text-sm font-bold">
                        Logo da Empresa
                    </p>
                    <p class="text-description mt-1 text-xs">
                        Clique para alterar
                    </p>
                    @error('logo')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex w-full gap-2">
                    {{-- Botão de Alterar/Upload --}}
                    <button type="button" onclick="document.getElementById('logoInput').click()"
                        class="ring-offset-background focus-visible:ring-primary border-border bg-background hover:bg-primary/20 inline-flex h-10 flex-1 cursor-pointer items-center justify-center gap-2 rounded-xl border px-4 py-2 text-sm font-semibold transition-colors focus-visible:outline-none">
                        <i class="bi bi-camera"></i>
                        {{ $logo || $currentLogo ? 'Alterar Logo' : 'Upload' }}
                    </button>

                    {{-- Botão de SALVAR LOGO (Disquete) - Só aparece se tiver um novo upload pendente --}}
                    @if ($logo)
                        <button type="button" wire:click="saveLogo" wire:loading.attr="disabled"
                            class="inline-flex h-10 w-10 cursor-pointer items-center justify-center rounded-xl border border-emerald-600 bg-emerald-50 text-emerald-600 transition-colors hover:bg-emerald-600 hover:text-white"
                            title="Salvar nova logo">
                            <i wire:loading.remove wire:target="saveLogo" class="bi bi-floppy"></i>
                            <i wire:loading wire:target="saveLogo" class="bi bi-arrow-repeat animate-spin"></i>
                        </button>
                    @endif

                    {{-- Botão de Deletar --}}
                    @if ($logo || $currentLogo)
                        <button type="button" wire:click="deleteLogo"
                            wire:confirm="Tem certeza que deseja remover a logo da empresa?" title="Excluir Logo"
                            class="inline-flex h-10 w-10 cursor-pointer items-center justify-center rounded-xl border border-red-600 bg-red-50 text-red-600 transition-colors hover:bg-red-600 hover:text-white">
                            <i wire:loading.remove wire:target="deleteLogo" class="bi bi-trash3"></i>
                            <i wire:loading wire:target="deleteLogo" class="bi bi-arrow-repeat animate-spin"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card de Dados da Empresa --}}
        <div
            class="text-card-foreground card-shadow rounded-xl border border-slate-100 bg-white shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between p-6">
                <h3 class="flex items-center gap-2 text-lg font-semibold tracking-tight">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-building2-icon lucide-building-2 text-primary">
                        <path d="M10 12h4" />
                        <path d="M10 8h4" />
                        <path d="M14 21v-3a2 2 0 0 0-4 0v3" />
                        <path d="M6 10H4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2" />
                        <path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16" />
                    </svg>
                    Dados da Empresa
                </h3>

                {{-- Botão para alternar entre Editar e Visualizar --}}
                <button type="button" wire:click="toggleEdit"
                    class="{{ $isEditing ? 'text-red-600' : 'text-primary' }} cursor-pointer text-sm font-medium hover:underline">
                    {{ $isEditing ? 'Cancelar' : 'Editar Informações' }}
                </button>
            </div>

            <div class="space-y-5 p-6 pt-0">
                <form wire:submit="save" class="grid grid-cols-1 gap-5 md:grid-cols-2">

                    {{-- Nome da Empresa --}}
                    <div class="space-y-2">
                        <label class="text-description flex items-center gap-2 text-sm font-semibold">
                            <i class="bi bi-person-vcard"></i>
                            Nome da Empresa
                        </label>
                        @if ($isEditing)
                            <input type="text" wire:model="name" class="input-modal py-6">
                        @else
                            <p class="bg-background rounded-xl border border-dashed border-slate-400 p-3 text-gray-700">
                                {{ $name }}</p>
                        @endif
                        @error('name')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Telefone --}}
                    <div class="space-y-2">
                        <label class="text-description flex items-center gap-2 text-sm font-semibold">
                            <i class="bi bi-telephone text-description"></i>
                            Telefone
                        </label>
                        @if ($isEditing)
                            <input type="text" wire:model="number" class="input-modal py-6"
                                x-mask:dynamic="$input.replace(/\D/g, '').length >= 11 ? '(99) 99999-9999' : '(99) 9999-9999'">
                        @else
                            {{-- Aqui usamos o Helper formatado para exibição --}}
                            <p class="bg-background rounded-xl border border-dashed border-slate-400 p-3 text-gray-700">
                                {{ strlen($number) == 11 ? preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $number) : preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $number) }}
                            </p>
                        @endif
                    </div>

                    {{-- Endereço --}}
                    <div class="space-y-2">
                        <label class="text-description flex items-center gap-2 text-sm font-semibold">
                            <i class="bi bi-geo-alt text-description"></i>
                            Endereço
                        </label>
                        @if ($isEditing)
                            <input type="text" wire:model="address" class="input-modal py-6">
                        @else
                            <p class="bg-background rounded-xl border border-dashed border-slate-400 p-3 text-gray-700">
                                {{ $address }}</p>
                        @endif
                    </div>

                    {{-- CNPJ --}}
                    <div class="space-y-2">
                        <label class="text-description flex items-center gap-2 text-sm font-semibold">
                            <i class="bi bi-file-earmark-text text-description"></i>
                            CNPJ
                        </label>
                        @if ($isEditing)
                            <input type="text" wire:model="cnpj" class="input-modal py-6"
                                x-mask="99.999.999/9999-99">
                        @else
                            <p
                                class="bg-background rounded-xl border border-dashed border-slate-400 p-3 text-gray-700">
                                {{ preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj) }}
                            </p>
                        @endif
                    </div>

                    {{-- Botão de Salvar (Só aparece se estiver editando) --}}
                    @if ($isEditing)
                        <button type="submit" wire:loading.attr="disabled" wire:target="save"
                            class="modal-button col-span-1 mt-4 gap-2 md:col-span-2">
                            <span wire:loading.remove wire:target="save">
                                <i class="bi bi-floppy"></i> Salvar Alterações
                            </span>
                            <span wire:loading wire:target="save">
                                <i class="bi bi-arrow-repeat inline-block animate-spin"></i> Salvando...
                            </span>
                        </button>
                    @endif

                </form>
            </div>
        </div>
    </div>
</div>
