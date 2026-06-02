<?php

namespace App\Services\Paystack;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    public function __construct(
        public readonly string $secretKey,
        public readonly string $baseUrl = 'https://api.paystack.co',
    ) {
        if ($secretKey === '') {
            throw new \InvalidArgumentException('Paystack secret key is not configured. Set PAYSTACK_SECRET_KEY in your .env file.');
        }
    }

    public static function fromConfig(): self
    {
        return new self(
            secretKey: (string) config('services.paystack.secret_key', ''),
            baseUrl: (string) config('services.paystack.base_url', 'https://api.paystack.co'),
        );
    }

    /**
     * Initialize a transaction. Returns Paystack's `data` payload which includes
     * `authorization_url` and `access_code`.
     *
     * @return array<string, mixed>
     *
     * @throws PaystackException
     */
    public function initialize(InitializeTransactionData $data): array
    {
        $response = Http::withToken($this->secretKey)
            ->acceptJson()
            ->asJson()
            ->post($this->baseUrl.'/transaction/initialize', $data->toArray());

        return $this->handle($response, 'initialize');
    }

    /**
     * Verify a transaction by its reference. Returns Paystack's `data` payload.
     * Always call this on the callback — never trust the redirect query string.
     *
     * @return array<string, mixed>
     *
     * @throws PaystackException
     */
    public function verify(string $reference): array
    {
        $response = Http::withToken($this->secretKey)
            ->acceptJson()
            ->get($this->baseUrl.'/transaction/verify/'.rawurlencode($reference));

        return $this->handle($response, 'verify');
    }

    /**
     * @return array<string, mixed>
     *
     * @throws PaystackException
     */
    private function handle(Response $response, string $operation): array
    {
        if ($response->failed()) {
            Log::warning('Paystack HTTP failure', [
                'operation' => $operation,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            throw new PaystackException(
                $response->json('message') ?? 'Paystack request failed',
                $response->status(),
                $response->json() ?? [],
            );
        }

        $body = $response->json();

        if (! ($body['status'] ?? false)) {
            Log::warning('Paystack business-logic failure', [
                'operation' => $operation,
                'body' => $body,
            ]);

            throw new PaystackException(
                $body['message'] ?? 'Paystack returned an unsuccessful response',
                $response->status(),
                $body ?? [],
            );
        }

        return $body['data'] ?? [];
    }
}
