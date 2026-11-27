<?php

namespace App\Enums;

enum ActivityPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
}