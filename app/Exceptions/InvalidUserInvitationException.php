<?php

namespace App\Exceptions;

use Exception;

class InvalidUserInvitationException extends Exception
{
    protected $message = 'Запрошення не знайдено';

    protected $code = 404;

    public function render($request)
    {
        return response()->json(['message' => $this->message], $this->code);
    }
}
