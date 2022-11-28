<?php

namespace AllDressed\Laravel\Builders;

use AllDressed\Laravel\Client;
use AllDressed\Laravel\DeliverySchedule;
use AllDressed\Laravel\Exceptions\DeliveryScheduleNotFoundException;
use AllDressed\Laravel\Exceptions\MissingPostalCodeException;
use AllDressed\Laravel\Exceptions\ZoneNotFoundException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Throwable;

class DeliveryScheduleBuilder extends Builder
{
    /**
     * Alias of the get method.
     *
     * @return \Illuminate\Support\Collection<int, \AllDressed\Laravel\DeliverySchedule>
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
     * Retrieve the delivery schedule that has the given id.
     *
     * @param  string  $id
     * @return \AllDressed\Laravel\DeliverySchedule|null
     */
    public function find(string $id): ?DeliverySchedule
    {
        return $this->withOption('id', $id)->first();
    }

    /**
     * Retrieve the first delivery schedule from the response.
     *
     * @return \AllDressed\Laravel\DeliverySchedule|null
     */
    public function first(): ?DeliverySchedule
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
     * @return \Illuminate\Support\Collection<int, \AllDressed\Laravel\DeliverySchedule>
     *
     * @throws \AllDressed\Laravel\Exceptions\MissingPostalCodeNotFoundException
     * @throws \AllDressed\Laravel\Exceptions\ZoneNotFoundException
     */
    public function get(): Collection
    {
        $postcode = Arr::get($this->options, 'postcode');

        throw_unless($postcode, MissingPostalCodeException::class);

        try {
            $client = resolve(Client::class);

            $endpoint = "zones/{$postcode}/schedules";

            if ($this->getOption('available')) {
                $endpoint = "{$endpoint}/available";
            } elseif ($id = $this->getOption('id')) {
                $endpoint = "{$endpoint}/{$id}";
            }

            $response = $client->get($endpoint);
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
                postcode: $postcode
            );
        }

        $data = $response->json('data');

        if (isset($id)) {
            $data = [$data];
        }

        return collect($data)->mapInto(DeliverySchedule::class);
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
            if ($id = $this->getOption('id')) {
                throw new DeliveryScheduleNotFoundException($id, $exception);
            }

            throw new ZoneNotFoundException($postcode, $exception);
        }

        throw $exception;
    }
}
