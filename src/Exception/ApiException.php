<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiException extends HttpException
{
    public function __construct(int $statusCode, string $message = '')
    {
        parent::__construct($statusCode, $message);
    }
}
