<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->company_id != null) {
            $user = auth()->user()->whereHas('company', function ($query) {
                $query->where('is_active', false);
            })->first();

            if ($user) {
                $request->user()->tokens()->delete();
                return response()->json([
                    'status'        => false,
                    'status_code'   => 401,
                    'error'         => trans('messages.staff_account_deactivate')
                ]);
            }
        }

        return $next($request);
    }

}
