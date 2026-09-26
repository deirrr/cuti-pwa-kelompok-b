<?php

namespace App\Http\Middleware;

use App\Models\Pengguna;
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
        $pengguna = $request->user();

        abort_unless(
            $pengguna instanceof Pengguna && $pengguna->memilikiSalahSatuPeran($peranDiizinkan),
            Response::HTTP_FORBIDDEN,
        );

        return $next($request);
    }
}
