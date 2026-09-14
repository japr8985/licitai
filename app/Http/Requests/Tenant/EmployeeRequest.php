<?php

namespace App\Http\Requests\Tenant;

use App\Traits\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EmployeeRequest extends FormRequest
{
    use ApiResponse;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Datos del Usuario
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'nullable|string|min:8', // Opcional, podemos generar uno por defecto
            
            // Datos del Empleado
            'job_title' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            
            // Archivos
            'avatar_uuid' => 'nullable|string',
            'cv_uuid' => 'nullable|string',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $response = $this->error('The given data was invalid', 422, $validator->errors());

        throw new HttpResponseException($response);
    }
}
