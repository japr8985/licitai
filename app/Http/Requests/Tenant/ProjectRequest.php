<?php

namespace App\Http\Requests\Tenant;

use App\Http\Requests\Tenant\BaseApiRequest;
use App\Traits\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProjectRequest extends BaseApiRequest
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
            'title' => 'required|string',
            'issuing_entity' => 'required|string',
            'valuation' => 'nullable|numeric',
            'upload_uuid' => 'required|string',
            'file_name' => 'required|string',
            'total_chunks' => 'required|integer'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = $this->error('The given data was invalid', 422, $validator->errors());

        throw new HttpResponseException($response);
    }
}
