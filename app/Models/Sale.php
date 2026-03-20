<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_value',
        'payment_method',
        'received_value'
    ];

    // Uma venda PERTENCE A um usuário (quem operou o caixa)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Uma venda TEM MUITOS itens (Coca, X-Bacon, Sorvete...)
    public function items()
    {
        // Aqui chamamos a função de "items" para ficar mais fácil de ler depois
        return $this->hasMany(SaleItem::class);
    }
}
