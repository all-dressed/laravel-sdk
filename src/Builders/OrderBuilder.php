<?php

namespace AllDressed\Builders;

use AllDressed\Client;
use AllDressed\Exceptions\MissingMenuException;
use AllDressed\Exceptions\MissingSubscriptionException;
use AllDressed\Menu;
use AllDressed\Order;
use AllDressed\Subscription;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Throwable;

class OrderBuilder extends RequestBuilder
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

        $endpoint = "subscriptions/{$subscription->id}/orders";

        try {
            $response = resolve(Client::class)->get($endpoint);
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
            );
        }

        $data = $response->json('data');

        if (isset($id)) {
            $data = [$data];
        }

        return collect($data)->mapInto(Order::class);
    }
}
