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

class DeliveryScheduleBuilder extends RequestBuilder
{
    /**
     * Retrieve the available delivery schedules.
     *
     * @param  bool  $backoff
     * @return static
     */
    public function available(bool $backoff = false): static
    {
        return $this->withOption('available', true)
            ->withOption('backoff', $backoff);
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
        $id = null;
        $postcode = null;

        try {
            $client = resolve(Client::class);

            $postcode = $this->getOption('postcode');

            $payload = [];

            if ($id = $this->getOption('id')) {
                $endpoint = "schedules/{$id}";

                if ($postcode) {
                    $endpoint = "zones/{$postcode}/{$endpoint}";
                }
            } elseif ($this->getOption('available')) {
                throw_unless($postcode, MissingPostalCodeException::class);

                $endpoint = "zones/{$postcode}/schedules/available";

                $payload = [
                    'backoff' => $this->getOption('backoff'),
                ];
            }

            $response = $client->get($endpoint, $payload);
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
                id: $id,
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
     * @param  string|null  $id
     * @param  string|null  $postcode
     * @return void
     */
    protected function throw(Throwable $exception, string $id = null, string $postcode = null): void
    {
        if ($exception->getCode() == 404) {
            if ($id) {
                throw new DeliveryScheduleNotFoundException($id, $exception);
            }

            if ($postcode) {
                throw new ZoneNotFoundException($postcode, $exception);
            }
        }

        throw $exception;
    }
}
