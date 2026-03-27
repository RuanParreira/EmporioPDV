<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToEnterprise
{
    protected static function bootBelongsToEnterprise()
    {
        static::addGlobalScope('enterprise', function (Builder $builder) {
            if (Auth::check() && Auth::user()->enterprise_id) {
                $builder->where(
                    $builder->qualifyColumn('enterprise_id'),
                    Auth::user()->enterprise_id
                );
            }
        });

        static::creating(function ($model) {
            if (empty($model->enterprise_id) && Auth::check() && Auth::user()->enterprise_id) {
                $model->enterprise_id = Auth::user()->enterprise_id;
            }
        });
    }
}
