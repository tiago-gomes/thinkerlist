<?php
namespace App\Enums;

enum RecurringTimeDay: int
{
    case FULL_DAY = 1;
    case MORNING = 2;
    case AFTERNOON = 3;
    case NIGHT = 4;
}
