<?php

namespace AllDressed;

use AllDressed\Builders\NullBuilder;
use AllDressed\Builders\OrderBuilder;

class Order extends Base
{
    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\OrderBuilder
     */
    public static function query(): OrderBuilder
    {
        return OrderBuilder::make();
    }
}
