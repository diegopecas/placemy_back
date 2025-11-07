<?php

namespace App\Domain\Shared\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    protected $code = 404;
    
    public function __construct(string $message = 'Registro no encontrado', int $code = 404)
    {
        parent::__construct($message, $code);
    }
    
    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'error_type' => 'not_found'
        ], $this->code);
    }
}