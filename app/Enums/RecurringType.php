<?php
namespace App\Enums;

enum RecurringType: int
{
    case NONE = 0;
    case DAILY = 1;
    case WEEKLY = 2;
    case MONTHLY = 3;
    case YEARLY = 4;
}
