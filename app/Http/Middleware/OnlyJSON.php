<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OnlyJSON
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $acceptHeader = $request->header('Accept');
        if ($acceptHeader != 'application/json') {
            return response()->json(['errors' => 1, 'message' => 'Eroare, va rugam reincercati!'], 400);
        }
        
        return response()->json( $next($request)->original );
    }
}
