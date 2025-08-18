<?php

namespace App\Exceptions\Chat;

use Exception;

/**
 * Chat message error class
 *
 * @package App\Exceptions\Chat
 */
class ChatMessageException extends Exception
{
    public function __construct(
        $message,
        $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
