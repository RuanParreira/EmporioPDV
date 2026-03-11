<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'notes'
    ];

    // Um item PERTENCE A uma venda específica
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    // Um item PERTENCE A um produto do cardápio
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
