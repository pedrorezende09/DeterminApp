<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DeleteCategoryRequest extends FormRequest
{
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'new_category_id' => [
                'required',
                'integer',
                \Illuminate\Validation\Rule::exists('categories', 'id')
                    ->where('user_id', $this->user()->id)
                    ->where('is_active', true)
                    ->withoutTrashed(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'new_category_id.required' => 'A categoria de destino é obrigatória.',
            'new_category_id.integer' => 'A categoria de destino deve ser um identificador válido.',
            'new_category_id.exists' => 'A categoria de destino não existe, não pertence a você ou está inativa.',
        ];
    }
}
