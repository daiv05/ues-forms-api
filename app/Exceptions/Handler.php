<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use App\Traits\ResponseTrait;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    use ResponseTrait;
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
        $this->reportable(function (Throwable $e) {});

        $this->renderable(function (Throwable $e) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $message = '';

            if ($e instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
                $code = Response::HTTP_FORBIDDEN;
                $message = 'No tienes permiso para acceder a este recurso';
            } else if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                $code = Response::HTTP_NOT_FOUND;
                $message = 'La ruta solicitada no existe';
            } else if ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
                error_log($e);
                $code = Response::HTTP_METHOD_NOT_ALLOWED;
                $message = 'El método de la petición no es válido';
            } else if ($e instanceof \Illuminate\Database\QueryException) {
                $code = Response::HTTP_INTERNAL_SERVER_ERROR;
                $message = 'Ha ocurrido un error inesperado';
            }

            return $this->error($message, $e->getMessage(), $code);
        });
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof AuthenticationException) {
            return $this->error('No estás autenticado', $e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
        return parent::render($request, $e);
    }
}
