<?php

namespace App\Models;

use App\Traits\BelongsToEnterprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory, BelongsToEnterprise;

    protected $fillable = [
        'enterprise_id',
        'value',
        'paid_at',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class);
    }
}
