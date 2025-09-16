<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ManagerAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('manager')->check()) {
            return redirect()->route('manager.login');
        }

        $manager = Auth::guard('manager')->user();
        
        if (!$manager->isActive()) {
            Auth::guard('manager')->logout();
            return redirect()->route('manager.login')
                ->withErrors(['username' => '비활성화된 계정입니다.']);
        }

        return $next($request);
    }
}