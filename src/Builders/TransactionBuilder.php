<?php

namespace AllDressed\Builders;

use AllDressed\Client;
use AllDressed\Currency;
use AllDressed\Exceptions\MissingPaymentMethodException;
use AllDressed\Exceptions\MissingSubscriptionException;
use AllDressed\Exceptions\NotImplementedException;
use AllDressed\PaymentMethod;
use AllDressed\Subscription;
use AllDressed\Transaction;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Throwable;

class TransactionBuilder extends RequestBuilder
{
    /**
     * Send the request to create a customer.
     *
     * @param  \AllDressed\Subscription|null  $subscription
     * @param  \AllDressed\PaymentMethod|null  $method
     * @return \AllDressed\Transaction
     *
     * @throws \AllDressed\Exceptions\MissingCurrencyException
     * @throws \AllDressed\Exceptions\MissingCustomerException
     */
    public function create(Subscription $subscription = null, PaymentMethod $method = null): Transaction
    {
        $client = resolve(Client::class);

        throw_unless(
            $subscription ??= $this->getOption('subscription'),
            MissingSubscriptionException::class
        );

        $method ??= $this->getOption('method') ?? $subscription->payment_method;

        throw_unless($method, MissingPaymentMethodException::class);

        try {
            $response = $client->post(
                "subscriptions/{$subscription->id}/payments",
                [
                    'menu' => $this->getOption('menu'),
                    'payment_method' => $method->id,
                ]
            );

            return Transaction::make($response->json('data'));
        } catch (RequestException $exception) {
            $this->throw($exception);
        }
    }

    /**
     * Indicates the customer of the subscription.
     *
     * @param  string  $menu
     * @return static
     */
    public function forMenu(string $menu): static
    {
        return $this->withOption('menu', $menu);
    }

    /**
     * Indicates the currency of the subscription.
     *
     * @param  \AllDressed\Subscription  $subscription
     * @return static
     */
    public function forSubscription(Subscription $subscription): static
    {
        return $this->withOption('subscription', $subscription);
    }

    /**
     * Retrieve the subscriptions.
     *
     * @return \Illuminate\Support\Collection<int, \AllDressed\Subscription>
     */
    public function get(): Collection
    {
        throw new NotImplementedException;
    }

    /**
     * Set the payment method of the request.
     *
     * @param  \AllDressed\PaymentMethod  $method
     * @return static
     */
    public function setPaymentMethod(PaymentMethod $method): static
    {
        return $this->withOption('payment_method', $method);
    }

    /**
     * Throw a new friendly exception based on the existing exception.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    protected function throw(Throwable $exception): void
    {
        dd($exception);

        throw $exception;
    }
}
