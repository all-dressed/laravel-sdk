<?php

namespace AllDressed\Builders;

use AllDressed\Client;
use AllDressed\Constants\DiscountValueType;
use AllDressed\Currency;
use AllDressed\Customer;
use AllDressed\Discount;
use AllDressed\Exceptions\DiscountNotFoundException;
use AllDressed\Exceptions\MissingDiscountCodeException;
use AllDressed\Exceptions\MissingSubscriptionException;
use AllDressed\Subscription;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Throwable;

class DiscountBuilder extends RequestBuilder
{
    /**
     * Send the request to create a discount.
     *
     * @param  string|null  $code
     * @param  \Illuminate\Support\Collection<int, \AllDressed\DiscountValue>  $values
     * @param  int|null  $orders
     * @param  bool  $newCustomers
     * @param  bool  $newSubscriptions
     * @return \AllDressed\Discount
     */
    public function create(?string $code, Collection $values, ?int $orders, bool $newCustomers, bool $newSubscriptions): Discount
    {
        try {
            $endpoint = 'discounts';

            if ($customer = $this->getOption('customer')) {
                $endpoint = "customers/{$customer->id}/discounts";
            }

            $response = resolve(Client::class)->post($endpoint, array_filter([
                'code' => $code,
                'orders' => $orders,
                'new_customers' => $newCustomers,
                'new_subscriptions' => $newSubscriptions,
                'values' => $values,
                'reward' => array_filter($this->getOption('reward') ?? []),
            ]));

            return Discount::make($response->json('data'));
        } catch (RequestException $exception) {
            $this->throw($exception);
        }
    }

    /**
     * Send the request to delete a discount.
     *
     * @return bool
     */
    public function delete(): bool
    {
        try {
            throw_unless(
                $subscription = $this->getOption('subscription'),
                MissingSubscriptionException::class
            );

            $endpoint = "subscriptions/{$subscription->id}/discount";

            $response = resolve(Client::class)->delete($endpoint);

            return true;
        } catch (RequestException $exception) {
            $this->throw($exception);
        }
    }

    /**
     * Indicates the code of the discount.
     *
     * @param  string  $code
     * @return static
     */
    public function forCode(string $code): static
    {
        return $this->withOption('code', $code);
    }

    /**
     * Indicates the customer of the discount.
     *
     * @param  \AllDressed\Customer  $customer
     * @return static
     */
    public function forCustomer(Customer $customer): static
    {
        return $this->withOption('customer', $customer);
    }

    /**
     * Indicates the subscription of the discount.
     *
     * @param  \AllDressed\Subscription  $subscription
     * @return static
     */
    public function forSubscription(Subscription $subscription): static
    {
        return $this->withOption('subscription', $subscription);
    }

    /**
     * Retrieve the list of discounts.
     *
     * @return \Illuminate\Support\Collection<int, \AllDressed\Discount>
     *
     * @throws \AllDressed\Exceptions\DiscountNotFoundException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Throwable
     */
    public function get(): Collection
    {
        try {
            throw_unless(
                $code = $this->getOption('code'),
                MissingDiscountCodeException::class
            );

            $endpoint = "discounts/{$code}";

            $customer = $this->getOption('customer');

            $subscription = $this->getOption('subscription');

            $response = resolve(Client::class)->get($endpoint, array_filter([
                'customer' => optional($customer)->id,
                'subscription' => optional($subscription)->id,
            ]));

            $data = $response->json('data');

            if ($code) {
                $data = [$data];
            }

            return collect($data)->mapInto(Discount::class);
        } catch (RequestException $exception) {
            $this->throw($exception, $code);
        }
    }

    /**
     * Throw a new friendly exception based on the existing exception.
     *
     * @param  \Throwable  $exception
     * @param  string|null  $code
     * @return void
     *
     * @throws \AllDressed\Exceptions\DiscountNotFoundException
     * @throws \Throwable
     */
    protected function throw(Throwable $exception, string $code = null): void
    {
        if ($exception->getCode() == 404 && $code) {
            throw new DiscountNotFoundException($code, $exception);
        }

        throw $exception;
    }


    /**
     * Set the reward of the referral code in the request.
     *
     * @param  \AllDressed\Constants\DiscountValueType  $type
     * @param  int  $value
     * @param  \AllDressed\Currency  $currency
     * @return static
     */
    public function withReward(DiscountValueType $type, int $value, Currency $currency): static
    {
        return $this->withOption('reward', [
            'type' => $type->value,
            'value' => $value,
            'currency' => $currency->id,
        ]);
    }
}
