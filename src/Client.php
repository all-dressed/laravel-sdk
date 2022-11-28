<?php

namespace AllDressed\Laravel;

use AllDressed\Laravel\Exceptions\MissingAccountException;
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
