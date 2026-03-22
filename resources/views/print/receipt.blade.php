<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprovante de Venda #{{ $sale->id }}</title>
    <style>
        /* Reset básico e configurações de página para a impressora */
        @page {
            margin: 0;
            /* Remove as margens padrão do navegador (cabeçalhos e rodapés com URL) */
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            /* Fontes monoespaçadas ficam melhores em térmicas */
            font-size: 12px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
        }

        .ticket {
            width: 80mm;
            /* Largura exata da bobina padrão */
            padding: 5mm;
        }

        /* Classes de alinhamento e formatação */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .bold {
            font-weight: bold;
        }

        .mb-2 {
            margin-bottom: 5px;
        }

        .mt-2 {
            margin-top: 5px;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        /* Tabela de itens */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        th,
        td {
            padding: 2px 0;
        }

        th {
            border-bottom: 1px dashed #000;
            text-align: left;
        }

        /* Esconde elementos na tela, mostra só na impressão, se necessário */
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="ticket">
        <div class="text-center mb-2">
            <h2 class="bold" style="margin: 0; font-size: 16px;">Empório Do Açaí</h2>
            <p style="margin: 2px 0;">CNPJ: 00.000.000/0001-00</p>
            <p style="margin: 2px 0;">Rua Exemplo, 123 - Centro</p>
            <p style="margin: 2px 0;">Tel: (00) 0000-0000</p>
        </div>

        <div class="divider"></div>

        <div class="mb-2">
            <p style="margin: 2px 0;"><strong>Cupom Não Fiscal</strong></p>
            <p style="margin: 2px 0;">Data: {{ $sale->created_at->format('d/m/Y H:i:s') }}</p>
            <p style="margin: 2px 0;">Pedido Nº: {{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>

        <div class="divider"></div>

        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Qtd</th>
                    <th style="width: 55%;">Descrição</th>
                    <th class="text-right" style="width: 30%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->items as $item)
                    <tr>
                        <td>{{ $item->quantity }}</td>
                        <td>
                            {{ $item->product_name }}
                            @if ($item->notes)
                                <br><small style="font-size: 9px;">* {{ $item->notes }}</small>
                            @endif
                        </td>
                        <td class="text-right">R$ {{ number_format($item->unit_price * $item->quantity, 2, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <table style="font-size: 12px;">
            <tr>
                <td class="bold">TOTAL:</td>
                <td class="text-right bold">R$ {{ number_format($sale->total_value, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Forma de Pagamento:</td>
                <td class="text-right uppercase">{{ ucfirst($sale->payment_method) }}</td>
            </tr>
            @if ($sale->payment_method === 'dinheiro' && $sale->received_value)
                <tr>
                    <td>Valor Recebido:</td>
                    <td class="text-right">R$ {{ number_format($sale->received_value, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Troco:</td>
                    <td class="text-right">R$
                        {{ number_format($sale->received_value - $sale->total_value, 2, ',', '.') }}</td>
                </tr>
            @endif
        </table>

        <div class="divider"></div>

        <div class="text-center mt-2">
            <p style="margin: 2px 0;">Obrigado pela preferência!</p>
            <p style="margin: 2px 0;">Volte sempre.</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
            window.onafterprint = function() {
                window.close();
            };
        }
    </script>
</body>

</html>
