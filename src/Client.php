<?php

namespace AllDressed;

use AllDressed\Exceptions\MissingAccountException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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
     * @param  string|null  $body
     * @return void
     */
    public static function fake(string $path, string $body = null): void
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
        Http::fake(
            collect($fakes)
                ->mapWithKeys(static fn ($body, $path) => [
                    resolve(static::class)->getEndpoint($path) => Http::response($body),
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

        return Http::withToken($this->key)
            ->acceptJson()
            ->withOptions([
                'verify' => config('all-dressed.request.verify'),
            ])
            ->{$method}($url, $payload)
            ->throw();
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
