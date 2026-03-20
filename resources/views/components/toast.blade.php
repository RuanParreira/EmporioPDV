<div x-data="{
    show: false,
    title: '',
    message: '',

    showToast(event) {
        this.title = event.detail.title || 'Sucesso!';
        this.message = event.detail.message || '';
        this.show = true;

        // Esconde automaticamente após 3 segundos
        setTimeout(() => this.show = false, 3000);
    }
}" {{-- Fica escutando o evento global 'notify' --}} @notify.window="showToast($event)" {{-- Posicionamento flutuante no canto inferior direito --}}
    class="fixed bottom-6 right-6 z-50 min-w-75" {{-- Animações suaves do Tailwind --}} x-show="show" style="display: none;"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-10"
    x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-10">

    <div class="bg-green-500 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-4">
        {{-- Ícone de Sucesso --}}
        <div class="bg-white/20 rounded-full min-w-8 w-8 h-8 flex items-center justify-center">
            <i class="bi bi-check-lg text-xl"></i>
        </div>

        {{-- Mensagem Dinâmica --}}
        <div class="flex-1">
            <h4 class="font-bold text-sm" x-text="title"></h4>
            <p class="text-xs text-green-100 mt-0.5" x-text="message"></p>
        </div>

        {{-- Botão para fechar manualmente --}}
        <button type="button" @click="show = false"
            class="ml-2 opacity-60 hover:opacity-100 transition-opacity cursor-pointer">
            <i class="bi bi-x-lg text-lg"></i>
        </button>
    </div>
</div>
