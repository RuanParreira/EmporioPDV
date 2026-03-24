<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'code',
        'name',
        'description',
        'price',
        'measure_unit',
        'active'
    ];

    // Um produto pertence a uma categoria
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    //Um produto tem muitos itens vendidos (historico de venda deles)
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
