<?php

namespace AllDressed;

use AllDressed\Exceptions\CardException;
use AllDressed\Exceptions\MissingAccountException;
use AllDressed\Exceptions\ValidationException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class Client
{
    /**
     * Create a new instance of the client.
     *
     * @param  string  $key
     * @param  string|null  $account
     */
    public function __construct(protected string $key, protected ?string $account)
    {
        //
    }

    /**
     * Fake the response of a given endpoint.
     *
     * @param  string  $path
     * @param  \Illuminate\Http\Client\Response|string|null  $body
     * @return void
     */
    public static function fake(string $path, $body = null): void
    {
        static::fakes([
            $path => $body,
        ]);
    }

    /**
     * Fake the response of a given endpoints.
     *
     * @param  array  $fakes
     * @return void
     */
    public static function fakes(array $fakes): void
    {
        $client = resolve(static::class);

        Http::fake(
            collect($fakes)
                ->mapWithKeys(static fn ($body, $path) => [
                    $client->getEndpoint($path) => is_string($body)
                        ? Http::response($body)
                        : $body,
                ])
                ->toArray()
        );
    }

    /**
     * Send a GET request to the given API endpoint.
     *
     * @param  string  $endpoint
     * @param  array  $query
     * @return \Illuminate\Http\Client\Response
     */
    public function get(string $endpoint, array $query = []): Response
    {
        return $this->send('get', $endpoint, $query);
    }

    /**
     * Retrieve the full url for the given endpoint.
     *
     * @param  string  $path
     * @return string
     */
    public function getEndpoint(string $path): string
    {
        return Str::of(rtrim(config('all-dressed.api.base'), '/'))
            ->append("/accounts/{$this->account}/")
            ->append(ltrim($path, '/'));
    }

    /**
     * Parse the exception to have a more user friendly exception.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    protected function parseException(Throwable $exception): void
    {
        Log::debug($exception->getMessage());

        if ($exception->getCode() === 422) {
            $exception = new ValidationException($exception->response);

            if ($exception->hasError('number')) {
                throw new CardException(
                    'number',
                    $exception->getErrorMessage('number')
                );
            }

            throw $exception;
        }

        throw $exception;
    }

    /**
     * Send a POST request to the given API endpoint.
     *
     * @param  string  $endpoint
     * @param  array  $payload
     * @return \Illuminate\Http\Client\Response
     */
    public function post(string $endpoint, array $payload = []): Response
    {
        return $this->send('post', $endpoint, $payload);
    }

    /**
     * Send a PUT request to the given API endpoint.
     *
     * @param  string  $endpoint
     * @param  array  $payload
     * @return \Illuminate\Http\Client\Response
     */
    public function put(string $endpoint, array $payload = []): Response
    {
        return $this->send('put', $endpoint, $payload);
    }

    /**
     * Send a request to the given API endpoint.
     *
     * @param  string  $method
     * @param  string  $endpoint
     * @param  array  $payload
     * @return \Illuminate\Http\Client\Response
     */
    public function send(string $method, string $endpoint, array $payload = []): Response
    {
        throw_unless($this->account, MissingAccountException::class);

        $url = $this->getEndpoint($endpoint);

        Log::debug("Endpoint :: {$url}", $payload);

        try {
            return Http::withToken($this->key)
                ->acceptJson()
                ->withOptions([
                    'verify' => config('all-dressed.request.verify'),
                ])
                ->{$method}($url, $payload)
                ->throw();
        } catch (Throwable $exception) {
            $this->parseException($exception);
        }
    }

    /**
     * Set the account of the requests.
     *
     * @param  string  $id
     * @return static
     */
    public function useAccount(string $id): static
    {
        $this->account = $id;

        return $this;
    }
}
