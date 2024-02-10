<?php
namespace App\Enums;

enum ErrorCode: int
{
    case OK = 200;
    case CREATED = 201;
    case UNAUTHORIZED = 401;
    case BAD_REQUEST = 400;
    case INTERNAL_SERVER_ERROR = 500;
    case UNPROCESSABLE_ENTITY = 422;
}
