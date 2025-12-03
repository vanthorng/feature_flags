<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FeatureServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Intentionally empty: features are created dynamically by admins.
    }
}
