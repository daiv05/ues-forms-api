<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
        /**
         * Generate a standardized error response.
         *
         * @param string $message
         * @param int $statusCode
         * @return \Illuminate\Http\JsonResponse
         */
    }

    public function render($request, Throwable $e)
    {
        if($e instanceof AuthenticationException) {
            return $this->respondWithError('No estás autenticado', 401);
        }

        if ($e instanceof AuthorizationException) {
            return $this->respondWithError('No tienes permiso para acceder a esta ruta.', 403);
        }
        return parent::render($request, $e);
    }

    /*
     *
     * Helper para devolver respuestas JSON consistentes.
     *
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
    */
    protected function respondWithError(string $message, int $statusCode): JsonResponse
    {
        return response()->json([
            'error' => true,
            'message' => $message,
        ], $statusCode);
    }
}
