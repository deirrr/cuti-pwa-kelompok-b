<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PastikanPeran
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$peranDiizinkan): Response
    {
        abort_unless(
            in_array($request->user()?->peran?->value, $peranDiizinkan, true),
            Response::HTTP_FORBIDDEN,
        );

        return $next($request);
    }
}
