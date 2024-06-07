<?php

namespace App\Exceptions;

use Exception;

class AlreadyMemberInvitationException extends Exception
{
    protected $message = 'Користувач вже є учасником проекту';

    protected $code = 400;

    public function render($request)
    {
        return response()->json([
            'message' => $this->message,
        ], $this->code);
    }
}
