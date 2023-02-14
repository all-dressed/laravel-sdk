<?php

namespace AllDressed;

use AllDressed\Builders\PaymentMethodBuilder;

class PaymentMethod extends Base
{
    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\PaymentMethodBuilder
     */
    public static function query(): PaymentMethodBuilder
    {
        return PaymentMethodBuilder::make();
    }
}
