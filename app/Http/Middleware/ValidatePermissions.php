<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ResponseTrait;

class ValidatePermissions
{
    use ResponseTrait;

    public function handle(Request $request, Closure $next, ...$permisos): Response
    {
        if (!auth()->user()->checkPermissions($permisos)) {
            return $this->error('No tienes permiso para acceder a este recurso', 'No tienes permiso para acceder a este recurso', Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
