<?php

namespace AllDressed;

use AllDressed\Builders\CustomerBuilder;
use AllDressed\Constants\DiscountValueType;
use AllDressed\Exceptions\MissingBillingAddressException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Customer extends Base
{
    /**
     * Create a new customer instance.
     *
     * @param  iterable<TKey, TValue>  $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        $discounts = Arr::get($attributes, 'discounts', []);

        if (is_array($discounts)) {
            $attributes['discounts'] = collect($discounts)
                ->mapInto(Discount::class);
        }

        parent::__construct($attributes);
    }

    /**
     * Add a payment method to the customer's profile.
     *
     * @param  \AllDressed\PaymentGateway  $gateway
     * @param  \AllDressed\Card  $card
     * @param  \AllDressed\Address|null  $address
     * @return \AllDressed\PaymentMethod
     */
    public function addPaymentMethod(PaymentGateway $gateway, Card $card, Address $address = null): PaymentMethod
    {
        throw_unless(
            $address ??= $card->address,
            MissingBillingAddressException::class
        );

        return PaymentMethod::query()
            ->forGateway($gateway)
            ->forCustomer($this)
            ->setBillingAddress($address)
            ->create($card);
    }

    /**
     * Create a referral code.
     *
     * @param  string|null  $code
     * @param  \Illuminate\Support\Collection<int, \AllDressed\DiscountValue>  $values
     * @param  \AllDressed\Constants\DiscountValueType  $rewardType
     * @param  int  $rewardValue
     * @param  \AllDressed\Currency  $rewardCurrency
     * @param  int|null  $orders
     * @param  bool  $newCustomers
     * @param  bool  $newSubscriptions
     * @return \AllDressed\Discount
     */
    public function createReferralCode(?string $code, Collection $values, DiscountValueType $rewardType, int $rewardValue, Currency $rewardCurrency, int $orders = null, bool $newCustomers = true, bool $newSubscriptions = true): Discount
    {
        return Discount::query()
            ->forCustomer($this)
            ->withReward($rewardType, $rewardValue, $rewardCurrency)
            ->create($code, $values, $orders, $newCustomers, $newSubscriptions);
    }

    /**
     * Retrieve the invoices of the customer.
     *
     * @param  int  $page
     * @return \Illuminate\Support\Collection<int, \AllDressed\Invoice>
     */
    public function getInvoices(int $page = 1): Collection
    {
        return Invoice::query()->forCustomer($this)->setPage($page)->get();
    }

    /**
     * Retrieve the payment methods of the customer.
     *
     * @return \Illuminate\Support\Collection<int, \AllDressed\PaymentMethod>
     */
    public function getPaymentMethods(): Collection
    {
        return PaymentMethod::query()->forCustomer($this)->get();
    }

    /**
     * Retrieve the subscriptions of the customer.
     *
     * @param  bool  $menus
     * @return \Illuminate\Support\Collection
     */
    public function getSubscriptions(bool $menus = false): Collection
    {
        return Subscription::query()->forCustomer($this)->get($menus);
    }

    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\CustomerBuilder
     */
    public static function query(): CustomerBuilder
    {
        return CustomerBuilder::make();
    }

    /**
     * Send the request to create or update the customer.
     *
     * @return static
     */
    public function save(): static
    {
        if ($this->id === null) {
            return static::query()->create($this->getAttributes());
        }

        return $this->update();
    }

    /**
     * Send the request to update the customer.
     *
     * @return static
     */
    public function update(): static
    {
        $attributes = $this->getAttributes();

        static::query()->update(Arr::pull($attributes, 'id'), $attributes);

        return $this;
    }
}
