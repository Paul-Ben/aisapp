<?php

namespace App\Services\Paystack;

class PaystackException extends \Exception
{
    public function __construct(
        string $message,
        public readonly int $statusCode,
        public readonly array $response = [],
    ) {
        parent::__construct($message, $statusCode);
    }
}
