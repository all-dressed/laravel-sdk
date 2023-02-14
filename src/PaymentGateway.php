<?php

namespace AllDressed;

use AllDressed\Builders\NullBuilder;

class PaymentGateway extends Base
{
    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\NullBuilder
     */
    public static function query(): NullBuilder
    {
        return NullBuilder::make();
    }
}
