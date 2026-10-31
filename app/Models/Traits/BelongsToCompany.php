<?php

namespace App\Models\Traits;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToCompany
{
    /**
     * Boot the BelongsToCompany trait.
     * Automatically applies company_id scoping and assignment.
     */
    public static function bootBelongsToCompany(): void
    {
        // 1. Global query scope using Auth::hasUser() to avoid recursion
        static::addGlobalScope('company', function (Builder $builder) {
            if (Auth::hasUser()) {
                $user = Auth::user();
                // Contractors and workers only see records for their company
                if ($user->role !== 'super_admin' && ! empty($user->company_id)) {
                    $builder->where($builder->getModel()->getTable().'.company_id', $user->company_id);
                }
            }
        });

        // 2. Automatically assign company_id when creating new records
        static::creating(function ($model) {
            if (empty($model->company_id) && Auth::hasUser()) {
                $user = Auth::user();
                if ($user->role !== 'super_admin' && ! empty($user->company_id)) {
                    $model->company_id = $user->company_id;
                }
            }
        });
    }

    /**
     * Get the company that owns the model.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
