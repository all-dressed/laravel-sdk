<?php

namespace AllDressed\Builders;

use AllDressed\Client;
use AllDressed\Customer;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Throwable;

class CustomerBuilder extends RequestBuilder
{
    /**
     * Send the request to create a customer.
     *
     * @param  array  $payload
     * @return \AllDressed\Customer
     */
    public function create(array $payload): Customer
    {
        $client = resolve(Client::class);

        try {
            $response = $client->post('customers', $payload);

            return Customer::make($response->json('data'));
        } catch (RequestException $exception) {
            $this->throw($exception);
        }
    }

    /**
     * Retrieve the customers.
     *
     * @return \Illuminate\Support\Collection<int, \AllDressed\Customer>
     */
    public function get(): Collection
    {
        $client = resolve(Client::class);

        $endpoint = 'customers';

        if ($id = $this->getOption('id')) {
            $endpoint = "customers/{$id}";
        }

        try {
            $response = $client->get($endpoint);
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
            );
        }

        $data = $response->json('data');

        if ($id) {
            $data = [$data];
        }

        return collect($data)->mapInto(Customer::class);
    }

    /**
     * Throw a new friendly exception based on the existing exception.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    protected function throw(Throwable $exception): void
    {
        throw $exception;
    }

    /**
     * Update the information of a customer.
     *
     * @param  string  $id
     * @param  array  $payload
     * @return bool
     */
    public function update(string $id, array $payload): bool
    {
        $client = resolve(Client::class);

        try {
            $response = $client->put("customers/{$id}", $payload);
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
            );
        }

        return true;
    }
}
