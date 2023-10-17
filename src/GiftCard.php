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
     * Create a new query builder.
     */
    public static function query(): GiftCardBuilder
    {
        return GiftCardBuilder::make();
    }
}
