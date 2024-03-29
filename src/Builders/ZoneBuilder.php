<?php

namespace AllDressed\Builders;

use AllDressed\Client;
use AllDressed\Exceptions\ZoneNotFoundException;
use AllDressed\Zone;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Throwable;

class ZoneBuilder extends RequestBuilder
{
    /**
     * Retrieve the available delivery schedules.
     *
     * @return static
     */
    public function available(): static
    {
        return $this->withOption('available', true);
    }

    /**
     * Filter the zones that contains the given postcode.
     *
     * @param  string  $postcode
     * @return static
     */
    public function forPostcode(string $postcode): static
    {
        return $this->withOption('postcode', $postcode);
    }

    /**
     * Retrieve the list of zones.
     *
     * @return \Illuminate\Support\Collection<int, \AllDressed\Zone>
     *
     * @throws \AllDressed\Exceptions\ZoneNotFoundException
     */
    public function get(): Collection
    {
        $postcode = Arr::get($this->options, 'postcode');

        try {
            $client = resolve(Client::class);

            $endpoint = 'zones';

            if ($postcode = $this->getOption('postcode')) {
                $endpoint = "{$endpoint}/{$postcode}";
            }

            $response = $client->get($endpoint);

            $data = $response->json('data');
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
                postcode: $postcode
            );
        }

        if ($postcode) {
            $data = [$data];
        }

        return collect($data)->mapInto(Zone::class);
    }

    /**
     * Throw a new friendly exception based on the existing exception.
     *
     * @param  \Throwable  $exception
     * @param  string|null  $postcode
     * @return void
     */
    protected function throw(Throwable $exception, string $postcode = null): void
    {
        if ($exception->getCode() == 404 && $postcode) {
            throw new ZoneNotFoundException($postcode, $exception);
        }

        throw $exception;
    }
}
