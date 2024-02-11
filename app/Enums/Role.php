<?php
namespace App\Enums;

enum Role: int
{
    case ADMIN = 0;
    case MANAGER = 1;
    case CUSTOMER = 2;
}
