<?php

namespace AllDressed\Builders;

use AllDressed\Client;
use AllDressed\Constants\DiscountValueType;
use AllDressed\Currency;
use AllDressed\Customer;
use AllDressed\Discount;
use AllDressed\Exceptions\MissingDiscountCodeException;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Throwable;

class DiscountBuilder extends RequestBuilder
{
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
     * Retrieve the list of discounts.
     *
     * @return \Illuminate\Support\Collection<int, \AllDressed\Discount>
     *
     * @throws \Exception
     */
    public function get(): Collection
    {
        try {
            throw_unless(
                $code = $this->getOption('code'),
                MissingDiscountCodeException::class
            );

            $endpoint = "discounts/{$code}";

            $response = resolve(Client::class)->get($endpoint);

            $data = $response->json('data');

            if ($code) {
                $data = [$data];
            }

            return collect($data)->mapInto(Discount::class);
        } catch (RequestException $exception) {
            $this->throw($exception);
        }
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
     * Set the reward of the referral code in the request.
     *
     * @param  \AllDressed\Constants\DiscountValueType  $rewardType
     * @param  int  $rewardValue
     * @param  \AllDressed\Currency  $rewardCurrency
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
