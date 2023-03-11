<?php

namespace AllDressed\Builders;

use AllDressed\Client;
use AllDressed\Constants\DiscountValueType;
use AllDressed\Currency;
use AllDressed\Customer;
use AllDressed\Discount;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Throwable;

class DiscountBuilder extends RequestBuilder
{
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
     * @return \AllDressed\Discount
     */
    public function create(?string $code, Collection $values): Discount
    {
        try {
            $endpoint = 'discounts';

            if ($customer = $this->getOption('customer')) {
                $endpoint = "customers/{$customer->id}/discounts";
            }

            $response = resolve(Client::class)->post($endpoint, array_filter([
                'code' => $code,
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
        throw new Exception('Method not implemented yet.');
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
