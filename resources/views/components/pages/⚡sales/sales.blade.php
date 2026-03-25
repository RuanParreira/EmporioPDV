<div class="space-y-4 p-6">
    {{-- Titulo --}}
    <div class="flex flex-col justify-between lg:flex-row">
        <x-titulo titulo="Histórico de Vendas" descricao="Consulte todas as vendas realizadas" />
    </div>

    <div class="table-default">
        <table>
            <thead>
                <tr>
                    <th>
                        N°
                    </th>
                    <th>
                        data
                    </th>
                    <th>
                        produtos
                    </th>
                    <th>
                        total
                    </th>
                    <th class="text-right">
                        pagamento
                    </th>
                    @can('viewAny', App\Models\Sale::class)
                        <th class="text-right">
                            ações
                        </th>
                    @endcan

                </tr>
            </thead>
            <tbody>
                @forelse ($this->sales as $sale)
                    <tr wire:key="sale-{{ $sale->id }}">
                        <td class="text-primary font-bold">
                            #{{ str_pad($sale->id, 4, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="text-description text-sm">
                            {{ $sale->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="text-sm">
                            <div class="flex flex-wrap gap-2">
                                @foreach ($sale->items as $item)
                                    @php $qty = (float) $item->quantity; @endphp

                                    <span class="inline-flex items-center gap-1">
                                        {{ $item->product_name }}
                                        @if ($qty != 1)
                                            <span
                                                class="rounded-full bg-gray-100 px-1.5 py-0.5 text-[10px] font-bold text-gray-600">
                                                x{{ $qty }}
                                            </span>
                                        @endif
                                        @if (!$loop->last)
                                            <span class="text-gray-300">|</span>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="font-bold">
                            R$ {{ number_format($sale->total_value, 2, ',', '.') }}
                        </td>
                        <td class="text-right">
                            <div class="flex flex-col items-end gap-1">
                                @php
                                    [$badgeClass, $iconClass] = match ($sale->payment_method) {
                                        'pix' => ['bg-blue-500/10 text-blue-600', 'bi bi-phone'],
                                        'cartao' => ['bg-yellow-500/10 text-yellow-600', 'bi bi-credit-card'],
                                        default => ['bg-green-500/10 text-green-600', 'bi bi-cash'],
                                    };
                                @endphp

                                <span
                                    class="{{ $badgeClass }} inline-flex w-fit items-center gap-1 rounded-lg px-2 py-1 text-xs font-bold capitalize">
                                    <i class="{{ $iconClass }}"></i>
                                    {{ $sale->payment_method ?? 'dinheiro' }}
                                </span>

                                {{-- Calcula e mostra o troco --}}
                                @if (in_array($sale->payment_method, ['dinheiro', null]) && $sale->received_value > $sale->total_value)
                                    @php
                                        $troco = $sale->received_value - $sale->total_value;
                                    @endphp
                                    <span class="mr-1 text-[10px] font-semibold text-gray-500">
                                        Troco: R$ {{ number_format($troco, 2, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        @can('delete', $sale)
                            <td class="text-right">
                                {{-- Botão: Ver Detalhes --}}
                                <button type="button"
                                    wire:click="$dispatch('open-sale-modal', { saleId: {{ $sale->id }} })"
                                    class="edit-button" title="Ver Detalhes">
                                    <i class="bi bi-eye"></i>
                                </button>

                                {{-- Botão: Imprimir Recibo --}}
                                <button type="button"
                                    wire:click="$dispatch('ask-to-print', { saleId: {{ $sale->id }} })"
                                    class="edit-button" title="Imprimir Recibo">
                                    <i class="bi bi-printer"></i>
                                </button>

                                {{-- Botão: Deletar --}}
                                <button type="button" wire:click="delete({{ $sale->id }})"
                                    wire:confirm='Você tem certeza que deseja apagar essa venda?' class="delete-button">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </td>
                        @endcan

                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-description py-4 text-center">
                            Nenhuma venda encontrada
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $this->sales->links() }}
    </div>

    <livewire:modals.show-sales />
</div>
