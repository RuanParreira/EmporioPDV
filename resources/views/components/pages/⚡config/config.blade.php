<div class="space-y-4 p-6">
    {{-- Titulo --}}
    <div class="flex flex-col justify-between lg:flex-row">
        <x-titulo titulo="Configuração" descricao="Gerencie as informações da sua empresa" />
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-slate-100 bg-white shadow-sm lg:col-span-1">
            <div class="flex flex-col items-center gap-5 p-8">
                <input type="file" accept="image/*" class="hidden">
                <div class="group relative cursor-pointer">
                    <div
                        class="border-primary/20 group-hover:border-primary/50 bg-primary/10 flex h-32 w-32 items-center justify-center overflow-hidden rounded-full border-4 transition-all duration-300 group-hover:shadow-lg">
                        <i class="bi bi-person text-primary/60 text-7xl"></i>
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
                </div>
                <div class="flex w-full gap-2">
                    <button
                        class="ring-offset-background focus-visible:ring-primary border-border bg-background hover:bg-primary/20 inline-flex h-10 flex-1 cursor-pointer items-center justify-center gap-2 whitespace-nowrap rounded-xl border px-4 py-2 text-sm font-semibold transition-colors hover:text-purple-950 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50">
                        <i class="bi bi-camera"></i>
                        Upload
                    </button>
                </div>
            </div>
        </div>

        {{-- Card de Dados da Empresa --}}
        <div
            class="text-card-foreground card-shadow rounded-xl border border-slate-100 bg-white shadow-sm lg:col-span-2">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="flex items-center gap-2 text-lg font-semibold tracking-tight">
                    <i class="bi bi-building-gear text-purple-900"></i>
                    Dados da Empresa
                </h3>
            </div>

            <div class="space-y-5 p-6 pt-0">
                {{-- O Form envolve o Grid para acionar o método save() no submit --}}
                <form wire:submit="save" class="grid grid-cols-1 gap-5 md:grid-cols-2">

                    <div class="space-y-2">
                        <label for="name" class="text-sm font-semibold">Nome da Empresa</label>
                        <div class="relative">
                            <i class="bi bi-building icon-input-search text-description"></i>
                            <input type="text" id="name" class="input-modal pl-10" wire:model="name">
                        </div>
                        @error('name')
                            <span class="text-xs font-medium text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="number" class="text-sm font-semibold">Telefone</label>
                        <div class="relative">
                            <i class="bi bi-telephone icon-input-search text-description"></i>
                            <input type="text" id="number" class="input-modal pl-10" wire:model="number"
                                placeholder="(00) 00000-0000"
                                x-mask:dynamic="$input.replace(/\D/g, '').length >= 11 ? '(99) 99999-9999' : '(99) 9999-9999'">
                        </div>
                        @error('number')
                            <span class="text-xs font-medium text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="address" class="text-sm font-semibold">Endereço</label>
                        <div class="relative">
                            <i class="bi bi-geo-alt icon-input-search text-description"></i>
                            <input type="text" id="address" class="input-modal pl-10" wire:model="address">
                        </div>
                        @error('address')
                            <span class="text-xs font-medium text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="cnpj" class="text-sm font-semibold">CNPJ</label>
                        <div class="relative">
                            <i class="bi bi-file-earmark-text icon-input-search text-description"></i>
                            <input type="text" id="cnpj" class="input-modal pl-10" wire:model="cnpj"
                                placeholder="00.000.000/0000-00" x-mask="99.999.999/9999-99">
                        </div>
                        @error('cnpj')
                            <span class="text-xs font-medium text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" wire:loading.attr="disabled" wire:target="save"
                        class="modal-button col-span-1 mt-4 gap-2 md:col-span-2">

                        {{-- Texto padrão --}}
                        <span wire:loading.remove wire:target="save">
                            <i class="bi bi-floppy"></i> Salvar Configurações
                        </span>

                        {{-- Texto de carregamento --}}
                        <span wire:loading wire:target="save">
                            <i class="bi bi-arrow-repeat inline-block animate-spin"></i>
                            Salvando...
                        </span>
                    </button>

                </form>
            </div>
        </div>
    </div>
</div>
