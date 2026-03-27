<?php

namespace App\Models;

use App\Traits\BelongsToEnterprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory, BelongsToEnterprise;

    protected $fillable = [
        'enterprise_id',
        'user_id',
        'total_value',
        'payment_method',
        'received_value'
    ];

    protected $casts = [
        'total_value' => 'decimal:2',
        'received_value' => 'decimal:2',
    ];

    // Uma venda pertence a uma empresa
    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class);
    }

    // Uma venda PERTENCE A um usuário (quem operou o caixa)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    // Uma venda TEM MUITOS itens (Coca, X-Bacon, Sorvete...)
    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }
}
