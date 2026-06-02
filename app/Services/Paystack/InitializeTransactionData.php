<?php

namespace App\Services\Paystack;

class InitializeTransactionData
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public readonly string $email,
        public readonly int $amount,
        public readonly string $reference,
        public readonly string $callbackUrl,
        public readonly array $metadata = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'amount' => $this->amount,
            'reference' => $this->reference,
            'callback_url' => $this->callbackUrl,
            'metadata' => $this->metadata,
        ];
    }
}
