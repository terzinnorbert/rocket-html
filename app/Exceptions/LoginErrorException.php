<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;

class LoginErrorException extends AuthorizationException
{
}
