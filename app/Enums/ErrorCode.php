<?php
namespace App\Enums;

enum ErrorCode: int
{
    case OK = 200;
    case UNAUTHORIZED = 401;
}
