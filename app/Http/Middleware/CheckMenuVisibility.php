<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use Symfony\Component\HttpFoundation\Response;

class CheckMenuVisibility
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $menuKey
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $menuKey): Response
    {
        $user = $request->user();
        if ($user) {
            $settingKey = $user->role . '_menu_' . $menuKey . '_visible';
            $visible = SystemSetting::get($settingKey, true);
            
            if (!$visible) {
                abort(403, 'Akses ke fitur ini dinonaktifkan oleh administrator.');
            }
        }
        
        return $next($request);
    }
}
