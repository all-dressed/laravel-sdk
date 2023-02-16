<?php

namespace AllDressed;

use AllDressed\Builders\SubscriptionBuilder;
use Illuminate\Support\Arr;

class Subscription extends Base
{
    /**
     * Create a new package instance.
     *
     * @param  iterable<TKey, TValue>  $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        if ($method = Arr::get($attributes, 'payment_method')) {
            Arr::set(
                $attributes,
                'payment_method',
                PaymentMethod::make($method)
            );
        }

        parent::__construct($attributes);
    }

    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\SubscriptionBuilder
     */
    public static function query(): SubscriptionBuilder
    {
        return SubscriptionBuilder::make();
    }

    /**
     * Pay for the given menu.
     *
     * @param  string  $date
     * @return \AllDressed\Transaction
     */
    public function pay(string $date): Transaction
    {
        return Transaction::query()
            ->forSubscription($this)
            ->forMenu($date)
            ->create();
    }
}
