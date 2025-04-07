<?php

namespace App\Http\Requests;

use App\Models\Seguridad\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules()
    {
        return [
            'nombre' => 'required|string|max:50',
            'apellido' => 'required|string|max:50',
            'fecha_nacimiento' => 'required|date',
            'telefono' => 'required|string|max:50',
            'carnet' => 'required|string|max:50|unique:users,carnet,' . $this->user()->id,
            'email' => 'required|email|max:255|unique:users,email,' . $this->user()->id,
        ];
    }

}
