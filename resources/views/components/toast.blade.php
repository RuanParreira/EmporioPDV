<div x-data="{
    show: false,
    title: '',
    message: '',
    type: 'success',

    showToast(event) {
        this.type = event.detail.type || 'success';
        this.title = event.detail.title || (this.type === 'error' ? 'Erro!' : 'Sucesso!');
        this.message = event.detail.message || '';
        this.show = true;

        // Esconde automaticamente após 3 segundos
        setTimeout(() => this.show = false, 3000);
    },

    // A função init() roda automaticamente quando o componente é carregado na tela
    init() {
        @if(session()->has('success'))
        this.showToast({ detail: { type: 'success', title: 'Sucesso!', message: '{{ session('success') }}' } });
        @endif

        @if(session()->has('error'))
        this.showToast({ detail: { type: 'error', title: 'Erro!', message: '{{ session('error') }}' } });
        @endif
    }
}" @notify.window="showToast($event)" class="fixed bottom-6 right-6 z-50 min-w-75" x-show="show"
    style="display: none;" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-10">

    {{-- Restante do seu HTML visual do Toast continua igual --}}
    <div class="text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-4"
        :class="type === 'error' ? 'bg-red-500' : 'bg-green-500'">

        <div class="bg-white/20 rounded-full min-w-8 w-8 h-8 flex items-center justify-center">
            <template x-if="type === 'success'">
                <i class="bi bi-check-lg text-xl"></i>
            </template>
            <template x-if="type === 'error'">
                <i class="bi bi-exclamation-triangle-fill text-lg"></i>
            </template>
        </div>

        <div class="flex-1">
            <h4 class="font-bold text-sm" x-text="title"></h4>
            <p class="text-xs mt-0.5" :class="type === 'error' ? 'text-red-100' : 'text-green-100'" x-text="message">
            </p>
        </div>

        <button type="button" @click="show = false"
            class="ml-2 opacity-60 hover:opacity-100 transition-opacity cursor-pointer">
            <i class="bi bi-x-lg text-lg"></i>
        </button>
    </div>
</div>
