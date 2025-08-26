<?php

namespace AllDressed\Builders;

use AllDressed\Client;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class TaxBuilder extends RequestBuilder
{
    /**
     * Filter the taxes per country.
     */
    public function forCountry(string $country) {
        return $this->withOption('country', $country);
    }

    /**
     * Filter the taxes per state.
     */
    public function forState(string $state) {
        return $this->withOption('state', $state);
    }

    /**
     * Filter the taxes per city.
     */
    public function forCity(string $city) {
        return $this->withOption('city', $city);
    }

    /**
     * Filter the taxes per postcode.
     */
    public function forPostcode(string $postcode) {
        return $this->withOption('postcode', $postcode);
    }

    /**
     * Retrieve the taxes.
     *
     * @return \Illuminate\Support\Collection
     */
    public function get(): Collection
    {
        $client = resolve(Client::class);

        $endpoint = 'shipping/taxes';

        try {
            $response = $client->post($endpoint, [
                'country' => $this->getOption('country'),
                'state' => $this->getOption('state'),
                'city' => $this->getOption('city'),
                'postcode' => $this->getOption('postcode'),
            ]);

            Log::debug($response->body());
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
            );
        }

        return collect($response->json());
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
}