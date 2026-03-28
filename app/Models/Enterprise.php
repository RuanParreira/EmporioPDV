<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enterprise extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cnpj',
        'number',
        'address',
        'logo',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'date',
        'is_active' => 'boolean',
    ];

    protected function formattedCnpj(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $cnpj = $attributes['cnpj'] ?? null;
                if (!$cnpj) return 'Não informado';
                return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj);
            }
        );
    }

    protected function formattedNumber(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $phone = $attributes['number'] ?? null;
                if (!$phone) return 'Não informado';
                $phone = preg_replace('/[^0-9]/', '', $phone);
                if (strlen($phone) === 11) {
                    return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $phone);
                } elseif (strlen($phone) === 10) {
                    return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $phone);
                }

                return $phone;
            }
        );
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }
}
