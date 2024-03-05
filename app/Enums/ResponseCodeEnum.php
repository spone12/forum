<?php

namespace App\Enums;

/**
 * Class ResponseCodesEnum
 *
 * @package App\Enums
 */
class ResponseCodeEnum
{
    const OK = 200;
    const CREATED = 201;
    const ACCEPTED = 202;
    const FOUND = 302;
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const SERVER_ERROR = 500;
}
