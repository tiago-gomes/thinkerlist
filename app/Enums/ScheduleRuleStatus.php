<?php
namespace App\Enums;

enum ScheduleRuleStatus: int
{
    case DRAFT = 0;
    case CANCEL = 1;
    case LIVE = 2;
}
