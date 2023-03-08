<?php

namespace AllDressed\Builders;

use AllDressed\Client;
use AllDressed\Exceptions\MissingMenuException;
use AllDressed\Exceptions\MissingSubscriptionException;
use AllDressed\Menu;
use AllDressed\Subscription;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Throwable;

class MenuBuilder extends RequestBuilder
{
    /**
     * Indicates the menu of the request.
     *
     * @param  \AllDressed\Menu  $menu
     * @return static
     */
    public function for(Menu $menu): static
    {
        return $this->withOption('menu', $menu);
    }

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

        $endpoint = "subscriptions/{$subscription->id}/menus";

        if ($id = $this->getOption('id')) {
            $endpoint = "{$endpoint}/{$id}";
        }

        try {
            $response = resolve(Client::class)->get($endpoint);
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
            );
        }

        $data = $response->json('data');

        if ($id) {
            $data = [$data];
        }

        return collect($data)->mapInto(Menu::class);
    }

    /**
     * Skip a menu.
     *
     * @return bool
     */
    public function skip(): bool
    {
        throw_unless(
            $subscription = $this->getOption('subscription'),
            MissingSubscriptionException::class
        );

        throw_unless(
            $menu = $this->getOption('menu'),
            MissingMenuException::class
        );

        try {
            resolve(Client::class)
                ->post("subscriptions/{$subscription->id}/{$menu->id}/skip");
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
            );
        }

        return true;
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
     * Unskip a menu.
     *
     * @return bool
     */
    public function unskip(): bool
    {
        throw_unless(
            $subscription = $this->getOption('subscription'),
            MissingSubscriptionException::class
        );

        throw_unless(
            $menu = $this->getOption('menu'),
            MissingMenuException::class
        );

        try {
            resolve(Client::class)
                ->post("subscriptions/{$subscription->id}/{$menu->id}/unskip");
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
            );
        }

        return true;
    }
}
