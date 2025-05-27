<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    /**
     * Respuesta exitosa
     */
    protected function success(string $message = 'Operación exitosa', $data = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Respuesta exitosa con paginación
     */
    protected function successPaginated(string $message = 'Operación exitosa', $data = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data['data'],
            'pagination' => [
                'from' => $data['pagination']['from'],
                'to' => $data['pagination']['to'],
                'per_page' => $data['pagination']['per_page'],
                'page' => $data['pagination']['page'],
                'nextPage' => $data['pagination']['nextPage'],
                'previousPage' => $data['pagination']['previousPage'],
                'totalPages' => $data['pagination']['totalPages'],
                'totalItems' => $data['pagination']['totalItems'],
            ],
        ], $status);
    }

    /**
     * Respuesta de error
     */
    protected function error(string $message = 'Ocurrió un error', string $errors, int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    /**
     * Respuesta de validación
     */
    protected function validationError(string $message = 'Errores de validación', $errors = [], int $status = 422): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
