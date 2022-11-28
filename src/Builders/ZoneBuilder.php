<?php

namespace AllDressed\Laravel\Builders;

use AllDressed\Laravel\Client;
use AllDressed\Laravel\Exceptions\ZoneNotFoundException;
use AllDressed\Laravel\Zone;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Throwable;

class ZoneBuilder extends Builder
{
    /**
     * Alias of the get method.
     *
     * @return \Illuminate\Support\Collection<int, \AllDressed\Laravel\Zone>
     */
    public function all(): Collection
    {
        return $this->get();
    }

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
     * Retrieve the first zone from the response.
     *
     * @return \AllDressed\Laravel\Zone|null
     */
    public function first(): ?Zone
    {
        return $this->get()->first();
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
     * @return \Illuminate\Support\Collection<int, \AllDressed\Laravel\Zone>
     *
     * @throws \AllDressed\Laravel\Exceptions\ZoneNotFoundException
     */
    public function get(): Collection
    {
        $postcode = Arr::get($this->options, 'postcode');

        try {
            $client = resolve(Client::class);

            $response = $client->get('zones');

            $data = $response->json();
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
                postcode: $postcode
            );
        }

        if ($postcode) {
            $zones = collect([
                new Zone($data),
            ]);
        } else {
            $zones = collect($data)->mapInto(Zone::class);
        }

        return $zones;
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
            throw new ZoneNotFoundException(
                __('Zone for :postcode not found.', [
                    'postcode' => $postcode,
                ]),
                404,
                $exception
            );
        }

        throw $exception;
    }
}
