<?php

namespace AllDressed;

use AllDressed\Builders\GiftCardBuilder;
use Illuminate\Support\Arr;

class GiftCard extends Base
{
    /**
     * Create a new customer instance.
     */
    public function __construct($attributes = [])
    {
        $currency = Arr::get($attributes, 'currency', []);

        if ($currency) {
            $attributes['currency'] = Currency::make($currency);
        }

        parent::__construct($attributes);
    }

    /**
     * Activate the gift card.
     */
    public function activate(Customer $customer): static
    {
        static::query()->activate(
            code: $this->code,
            customer: $customer
        );

        return $this;
    }

    /**
     * Create a new query builder.
     */
    public static function query(): GiftCardBuilder
    {
        return GiftCardBuilder::make();
    }
}
