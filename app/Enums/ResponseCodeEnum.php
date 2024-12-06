<?php

namespace App\Enums;

/**
 * Class response HTTP codes
 *
 * @var static OK
 * @var static CREATED
 * @var static ACCEPTED
 * @var static NO_CONTENT
 * @var static FOUND
 * @var static BAD_REQUEST
 * @var static UNAUTHORIZED
 * @var static FORBIDDEN
 * @var static NOT_FOUND
 * @var static TOO_MANY_REQUESTS
 * @var static SERVER_ERROR
 * @var static NOT_IMPLEMENTED
 * @var static BAD_GATEWAY
 * @var static SERVICE_UNAVAILABLE
 * @var static UNKNOWN_ERROR
 *
 * @package App\Enums
 */
class ResponseCodeEnum
{
    /** @var int OK */
    const OK = 200;
    /** @var int CREATED */
    const CREATED = 201;
    /** @var int ACCEPTED */
    const ACCEPTED = 202;
    /** @var int NO_CONTENT */
    const NO_CONTENT = 204;
    /** @var int FOUND */
    const FOUND = 302;
    /** @var int BAD_REQUEST */
    const BAD_REQUEST = 400;
    /** @var int UNAUTHORIZED */
    const UNAUTHORIZED = 401;
    /** @var int FORBIDDEN */
    const FORBIDDEN = 403;
    /** @var int NOT_FOUND */
    const NOT_FOUND = 404;
    /** @var int TOO_MANY_REQUESTS */
    const TOO_MANY_REQUESTS = 429;
    /** @var int SERVER_ERROR */
    const SERVER_ERROR = 500;
    /** @var int NOT_IMPLEMENTED */
    const NOT_IMPLEMENTED = 501;
    /** @var int BAD_GATEWAY */
    const BAD_GATEWAY = 502;
    /** @var int SERVICE_UNAVAILABLE */
    const SERVICE_UNAVAILABLE = 503;
    /** @var int UNKNOWN_ERROR */
    const UNKNOWN_ERROR = 520;
}
