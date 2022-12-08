<?php

namespace AllDressed\Builders;

use AllDressed\Client;
use AllDressed\DeliverySchedule;
use AllDressed\Exceptions\DeliveryScheduleNotFoundException;
use AllDressed\Exceptions\MissingPostalCodeException;
use AllDressed\Exceptions\ZoneNotFoundException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Throwable;

class DeliveryScheduleBuilder extends Builder
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
     * Filter the delivery schedules that belongs to the given postcode.
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
     * @return \Illuminate\Support\Collection<int, \AllDressed\DeliverySchedule>
     *
     * @throws \AllDressed\Exceptions\MissingPostalCodeNotFoundException
     * @throws \AllDressed\Exceptions\ZoneNotFoundException
     */
    public function get(): Collection
    {
        try {
            $client = resolve(Client::class);

            if ($id = $this->getOption('id')) {
                $endpoint = "schedules/{$id}";
            } elseif ($this->getOption('available')) {
                throw_unless(
                    $postcode = $this->getOption('postcode'),
                    MissingPostalCodeException::class
                );

                $endpoint = "zones/{$postcode}/schedules/available";
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
