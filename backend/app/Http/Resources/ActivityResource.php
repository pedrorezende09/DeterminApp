<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
        'id' => $this->id,
        'title' => $this->title,
        'description' => $this->description,
        'category_id' => $this->category_id,
        'type' => $this->type?->value,
        'priority' => $this->priority?->value,
        'due_date' => $this->due_date,
        'status' => $this->status?->value,
        'ends_at' => $this->ends_at,
        'completed_at' => $this->completed_at,
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
    ];
    }
}
