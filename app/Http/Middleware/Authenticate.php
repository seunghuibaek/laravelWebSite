<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // If the request is for the manager area, send to manager login
        if ($request->is('manager') || $request->is('manager/*')) {
            return route('manager.login');
        }

        // Default to front login
        return route('login');
    }
}
