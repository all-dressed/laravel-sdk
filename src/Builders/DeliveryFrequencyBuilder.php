<?php

namespace AllDressed\Builders;

use AllDressed\Client;
use AllDressed\DeliveryFrequency;
use AllDressed\DeliverySchedule;
use AllDressed\Exceptions\DeliveryScheduleNotFoundException;
use AllDressed\Exceptions\MissingDeliveryScheduleException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Throwable;

class DeliveryFrequencyBuilder extends Builder
{
    /**
     * Filter the delivery frequencies that belongs to the given delivery
     * schedule.
     *
     * @param  \AllDressed\DeliverySchedule|string  $schedule
     * @return static
     */
    public function forSchedule(DeliverySchedule|string $schedule): static
    {
        return $this->withOption(
            'schedule',
            $schedule instanceof DeliverySchedule ? $schedule->id : $schedule
        );
    }

    /**
     * Retrieve the list of zones.
     *
     * @return \Illuminate\Support\Collection<int, \AllDressed\DeliveryFrequency>
     */
    public function get(): Collection
    {
        $schedule = $this->getOption('schedule');

        throw_unless($schedule, MissingDeliveryScheduleException::class);

        try {
            $client = resolve(Client::class);

            $endpoint = "schedules/{$schedule}/frequencies";

            $response = $client->get($endpoint);
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
                schedule: $schedule,
            );
        }

        return collect($response->json('data'))
            ->map(static fn ($days) => new DeliveryFrequency([
                'days' => $days,
            ]));
    }

    /**
     * Throw a new friendly exception based on the existing exception.
     *
     * @param  \Throwable  $exception
     * @param  string|null  $schedule
     * @return void
     */
    protected function throw(Throwable $exception, string $schedule = null): void
    {
        if ($exception->getCode() == 404 && $schedule) {
            throw new DeliveryScheduleNotFoundException($schedule, $exception);
        }

        throw $exception;
    }
}
