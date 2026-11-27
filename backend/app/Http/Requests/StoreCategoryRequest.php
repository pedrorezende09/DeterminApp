<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:100',
                \Illuminate\Validation\Rule::unique('categories', 'name')
                    ->where('user_id', $this->user()->id),
            ],

            'user_id' => ['prohibited'],
            'is_system' => ['prohibited'],
            'is_active' => ['prohibited'],
        ];
    }


    public function messages(): array
    {
        return [
            'name.required' => 'O nome da categoria é obrigatório.',
            'name.string' => 'O nome da categoria deve ser um texto.',
            'name.max' => 'O nome da categoria não pode ultrapassar 100 caracteres.',
            'name.unique' => 'Você já possui uma categoria com esse nome.',
            'user_id.prohibited' => 'O usuário da categoria é definido automaticamente.',
            'is_system.prohibited' => 'O tipo da categoria é definido pelo sistema.',
            'is_active.prohibited' => 'O estado inicial da categoria é definido automaticamente.',
        ];
    }

}
