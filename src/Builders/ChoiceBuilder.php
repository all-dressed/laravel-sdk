<?php

namespace AllDressed\Builders;

use AllDressed\Choice;
use AllDressed\Client;
use AllDressed\Exceptions\MissingSubscriptionException;
use AllDressed\Subscription;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Throwable;

class ChoiceBuilder extends RequestBuilder
{
    /**
     * Set the menu of the query.
     *
     * @param  string  $menu
     * @return static
     */
    public function forMenu(string $menu): static
    {
        $menu = Str::of($menu)->when(
            Str::isUuid($menu) === false,
            static fn ($string) => $string->match('/^\d{4}-\d{2}-\d{2}$/')
        );

        throw_if($menu->isEmpty(), InvalidMenuIdentifierException::class);

        return $this->withOption('menu', $menu->toString());
    }

    /**
     * Retrieve the choices.
     *
     * @param  \Illuminate\Support\Collection<int, \AllDressed\Choice>  $choices
     * @return \Illuminate\Support\Collection<int, \AllDressed\Choice>
     */
    public function update(Collection $choices): Collection
    {
        throw_unless(
            $menu = $this->getOption('menu'),
            MissingMenuException::class
        );

        throw_unless(
            $subscription = $this->getOption('subscription'),
            MissingSubscriptionException::class
        );

        try {
            $endpoint = "subscriptions/{$subscription->id}/{$menu}/choices";

            resolve(Client::class)->put($endpoint, [
                'choices' => $choices->map->toPayload()->toArray(),
            ]);
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
            );
        }

        return $choices;
    }

    /**
     * Set the subscription of the request.
     *
     * @param  \AllDressed\Subscription  $subscription
     * @return static
     */
    public function ofSubscription(Subscription $subscription): static
    {
        return $this->withOption('subscription', $subscription);
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
     * Update the choices.
     *
     * @return \Illuminate\Support\Collection<int, \AllDressed\Choice>
     */
    public function get(): Collection
    {
        throw_unless(
            $menu = $this->getOption('menu'),
            MissingMenuException::class
        );

        throw_unless(
            $subscription = $this->getOption('subscription'),
            MissingSubscriptionException::class
        );

        try {
            $endpoint = "subscriptions/{$subscription->id}/{$menu}/choices";

            $response = resolve(Client::class)->get($endpoint);
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
            );
        }

        return collect($response->json('data'))->mapInto(Choice::class);
    }
}
