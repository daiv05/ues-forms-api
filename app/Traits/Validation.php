<?php

namespace App\Traits;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;

/**
 * Trait Validation (Handle a failed validation attempt)
 * @version 1.0.0
 * @package App\Traits
 * @property bool $stopOnFirstFailure
 * @method void failed(mixed $errors, int $code)
 * @method void failedValidation(ValidatorContract $validator)
 * @method void validateRequest($data, $rules, ...$params)
 * @method void validatePath($rules, ...$params)
 */
trait Validation
{
    use ResponseTrait;
    /**
     * Indicates whether validation should stop after the first rule failure.
     * @var bool
     */
    protected $stopOnFirstFailure = false;
    /**
     * Handle a failed validation attempt.
     * @param array $errors
     * @param int $code
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public function failed(mixed $errors, int $code)
    {
        throw new HttpResponseException($this->validationError($errors, status: $code));
    }

    public function  failedValidation(ValidatorContract $validator): void
    {
        $this->failed( $validator->errors(),Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Add additional rules to the request
     * @param array $rules
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public function validateRequest($data, $rules, ...$params): void
    {
        $validator = Validator::make($data, $rules, ...$params)
            ->stopOnFirstFailure($this->stopOnFirstFailure);

        if ($validator->fails()) {
            $this->failedValidation($validator);
        }
    }

    /**
     * Validar parametros de la ruta 
     * @param array $rules
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public function validatePath($rules, ...$params): void
    {
        $data = request()->route()->parameters();

        $validator = Validator::make($data, $rules, ...$params)
            ->stopOnFirstFailure($this->stopOnFirstFailure);

        if ($validator->fails()) {
            $this->failedValidation($validator);
        }
    }
}
