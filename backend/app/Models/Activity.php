<?php

namespace App\Models;

use App\Enums\ActivityPriority;
use App\Enums\ActivityStatus;
use App\Enums\ActivityType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{

    protected $fillable = [
        'description',
        'category_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => ActivityType::class,
            'status' => ActivityStatus::class,
            'priority' => ActivityPriority::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}