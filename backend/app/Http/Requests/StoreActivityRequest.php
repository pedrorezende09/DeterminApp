<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Enums\ActivityType;
use App\Enums\ActivityPriority;

class StoreActivityRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:150'],

            'description' => ['nullable', 'string'],

            'category_id' => [
                    'required',
                    'integer',
                    Rule::exists('categories', 'id')
                        ->where('user_id', $this->user()->id)
                        ->where('is_active', true)
                        ->whereNull('deleted_at'),
                    ],

            'type' => [
                        'required',
                        Rule::enum(ActivityType::class)
                        ],

            'priority' => [
                        'required_if:type,one_time',
                        'prohibited_unless:type,one_time',
                        Rule::enum(ActivityPriority::class),
                    ],

            'due_date' => [
                        'required_if:type,one_time',
                        'prohibited_unless:type,one_time',
                        'date',
                        'after_or_equal:today',
                        'before_or_equal:' . today()->addDays(29)->toDateString(),
                    ],

            'status' => ['prohibited'],

            'completed_at' => ['prohibited'],

            'ends_at' => ['prohibited'],

            'user_id' => ['prohibited'],
        ];
    }

    public function messages(): array
        {
            return [
                // Título
                'title.required' => 'O título da atividade é obrigatório.',
                'title.string' => 'O título deve ser um texto.',
                'title.max' => 'O título não pode ultrapassar 150 caracteres.',

                // Descrição
                'description.string' => 'A descrição deve ser um texto.',

                // Categoria
                'category_id.required' => 'Selecione uma categoria.',
                'category_id.integer' => 'A categoria informada é inválida.',
                'category_id.exists' => 'A categoria não existe, está inativa ou não pertence ao usuário.',

                // Tipo
                'type.required' => 'O tipo da atividade é obrigatório.',
                'type.enum' => 'O tipo da atividade informado é inválido.',

                // Prioridade
                'priority.required_if' => 'A prioridade é obrigatória para atividades pontuais.',
                'priority.prohibited_unless' => 'Atividades fixas não devem possuir prioridade.',
                'priority.enum' => 'A prioridade informada é inválida.',

                // Prazo
                'due_date.required_if' => 'O prazo é obrigatório para atividades pontuais.',
                'due_date.prohibited_unless' => 'Atividades fixas não devem possuir prazo de conclusão.',
                'due_date.date' => 'Informe uma data válida.',
                'due_date.after_or_equal' => 'O prazo não pode ser anterior à data atual.',
                'due_date.before_or_equal' => 'O prazo não pode ultrapassar 30 dias corridos a partir da criação.',

                // Campos controlados pelo backend
                'status.prohibited' => 'O status é definido automaticamente pelo sistema.',
                'completed_at.prohibited' => 'A data de conclusão é definida pelo sistema.',
                'ends_at.prohibited' => 'A data de encerramento é definida pelo sistema.',
                'user_id.prohibited' => 'O usuário da atividade é identificado automaticamente.',
            ];
        }
}
