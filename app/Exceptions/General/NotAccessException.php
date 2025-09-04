<?php

namespace App\Exceptions\General;

use Exception;

/**
 * Exception: No access
 *
 * @package App\Exceptions\General
 */
class NotAccessException extends Exception
{
    public function __construct(
        $message = '',
        $code = 0,
        \Throwable $previous = null
    ) {
        if (empty($message)) {
            $message = trans('errors.access');
        }
        parent::__construct($message, $code, $previous);
    }
}
