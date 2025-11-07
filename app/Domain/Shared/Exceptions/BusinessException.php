<?php

namespace App\Domain\Shared\Exceptions;

use Exception;

class BusinessException extends Exception
{
    protected $code = 400;
    
    public function __construct(string $message, int $code = 400)
    {
        parent::__construct($message, $code);
    }
    
    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'error_type' => 'business_error'
        ], $this->code);
    }
}