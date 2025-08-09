<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get current guard to check if admin
        $currentGuard = Auth::getDefaultDriver();

        // Retrieve maintenance mode status from settings
        $maintenanceMode = Setting::where('key', 'maintenance_mode')->value('plain_value');

        // Check if maintenance mode is enabled and the user is not an admin
        if ($maintenanceMode == '1' && $currentGuard != 'adminApi') {
            return response()->json(['error' => 'Site under maintenance.'], 503);
        }

        return $next($request);
    }
}
