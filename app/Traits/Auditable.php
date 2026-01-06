<?php

namespace App\Traits;

use App\Models\Audit;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    protected static function bootAuditable()
    {
        // Helper function to get current user ID
        $getUserId = function () {
            // Try default guard
            if (Auth::check()) {
                return Auth::id();
            }

            // If you have an admin guard
            if (Auth::guard('admin')->check()) {
                return Auth::guard('admin')->id();
            }

            // No user logged in (system action)
            return null;
        };

        // Created
        static::created(function ($model) use ($getUserId) {
            Audit::create([
                'auditable_type' => get_class($model),
                'auditable_id'   => $model->id,
                'user_id'        => $getUserId(),
                'action'         => 'created',
                'changes'        => $model->toArray(),
            ]);
        });

        // Updated
        static::updated(function ($model) use ($getUserId) {
            Audit::create([
                'auditable_type' => get_class($model),
                'auditable_id'   => $model->id,
                'user_id'        => $getUserId(),
                'action'         => 'updated',
                'changes'        => $model->getChanges(),
            ]);
        });

        // Deleted
        static::deleted(function ($model) use ($getUserId) {
            Audit::create([
                'auditable_type' => get_class($model),
                'auditable_id'   => $model->id,
                'user_id'        => $getUserId(),
                'action'         => 'deleted',
                'changes'        => $model->toArray(),
            ]);
        });
    }
}
