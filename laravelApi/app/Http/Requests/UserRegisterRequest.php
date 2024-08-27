<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Psy\CodeCleaner\FunctionReturnInWriteContextPass;

class UserRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // siapapun bisa registrasi
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
            // berdasarkan migration user_table 
            'username' => ['required', 'max:100'],
            'password' => ['required', 'max:100'],
            'name' => ['required', 'max:100'],
        ];
    }

    // custom tampilan error sesaui dengan user-api.json
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    } 
}
