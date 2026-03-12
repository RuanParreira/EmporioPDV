<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $inicioSemana = now()->startOfWeek(Carbon::MONDAY);
        $fimSemana = now()->endOfWeek(Carbon::SUNDAY);

        // Vendas Do Dia
        $vendasHoje = (float) Sale::whereDate('created_at', today())->sum('total_value');

        // Vendas Da semana
        $vendasSemana = (float) Sale::whereBetween('created_at', [$inicioSemana, $fimSemana])
            ->sum('total_value');

        // Vendas Do Mes
        $vendasMes = (float) Sale::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_value');

        // Quantidade de Vendas do dia
        $pedidosHoje = (int) Sale::whereDate('created_at', today())->count();

        // Calculo do TicketMedio
        $ticketMedio = $pedidosHoje > 0 ? $vendasHoje / $pedidosHoje : 0;

        // Produto Mais Vendido
        $maisVendido = SaleItem::query()
            ->select('products.name', DB::raw('SUM(sale_items.quantity) as total_qtd'))
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qtd')
            ->first();

        return view('dashboard', [
            'vendasHoje' => $vendasHoje,
            'vendasSemana' => $vendasSemana,
            'vendasMes' => $vendasMes,
            'pedidosHoje' => $pedidosHoje,
            'ticketMedio' => $ticketMedio,
            'maisVendido' => $maisVendido?->name ?? '-',
        ]);
    }
}
