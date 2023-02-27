<?php

namespace AllDressed\Builders;

use AllDressed\Client;
use AllDressed\Exceptions\MissingSubscriptionException;
use AllDressed\Menu;
use AllDressed\Subscription;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Throwable;

class MenuBuilder extends RequestBuilder
{
    /**
     * Indicates the subscription of the request.
     *
     * @param  \AllDressed\Subscription  $subscription
     * @return static
     */
    public function forSubscription(Subscription $subscription): static
    {
        return $this->withOption('subscription', $subscription);
    }

    /**
     * Retrieve the items.
     *
     * @return \Illuminate\Support\Collection<int, \AllDressed\Menu>
     */
    public function get(): Collection
    {
        throw_unless(
            $subscription = $this->getOption('subscription'),
            MissingSubscriptionException::class
        );

        try {
            $endpoint = "subscriptions/{$subscription->id}/menus";

            $response = resolve(Client::class)->get($endpoint);
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
            );
        }

        return collect($response->json('data'))->mapInto(Menu::class);
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
