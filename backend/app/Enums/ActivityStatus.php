<?php

namespace App\Enums;

enum ActivityStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Lost = 'lost';
    case Active = 'active';
    case Ended = 'ended';
}
