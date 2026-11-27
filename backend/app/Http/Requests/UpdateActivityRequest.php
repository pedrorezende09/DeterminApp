<?php

namespace App\Http\Requests;

use App\Enums\ActivityStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => ['sometimes', 'nullable', 'string'],

            'category_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('categories', 'id')
                    ->where('user_id', $this->user()->id)
                    ->where('is_active', true)
                    ->whereNull('deleted_at'),
            ],

            'title' => ['prohibited'],
            'type' => ['prohibited'],
            'priority' => ['prohibited'],
            'due_date' => ['prohibited'],
            'status' => ['prohibited'],
            'completed_at' => ['prohibited'],
            'ends_at' => ['prohibited'],
            'user_id' => ['prohibited'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (!$this->exists('category_id')) {
                    return;
                }

                $activity = $this->user()
                    ->activities()
                    ->find($this->route('id'));

                if (
                    $activity &&
                    !in_array($activity->status, [
                        ActivityStatus::Pending,
                        ActivityStatus::Active,
                    ], true)
                ) {
                    $validator->errors()->add(
                        'category_id',
                        'Não é possível alterar a categoria de uma atividade finalizada.'
                    );
                }
            },
        ];
    }
}