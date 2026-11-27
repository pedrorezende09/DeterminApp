<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_active' => [
                'required',
                'boolean',
            ],

            'name' => ['prohibited'],
            'user_id' => ['prohibited'],
            'is_system' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'is_active.required' => 'O estado da categoria é obrigatório.',
            'is_active.boolean' => 'O estado da categoria deve ser verdadeiro ou falso.',
            'name.prohibited' => 'O nome não pode ser alterado nesta operação.',
            'user_id.prohibited' => 'O usuário da categoria não pode ser alterado.',
            'is_system.prohibited' => 'O tipo da categoria não pode ser alterado.',
        ];
    }

}
