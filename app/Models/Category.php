<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    const DEFAULT_NAME = 'SemCategoria';
    protected $fillable = [
        'name'
    ];

    // Uma categoria TEM MUITOS produtos
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
