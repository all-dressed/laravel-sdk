<?php

namespace AllDressed;

use AllDressed\Builders\DiscountBuilder;

class Discount extends Base
{
    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\DiscountBuilder
     */
    public static function query(): DiscountBuilder
    {
        return DiscountBuilder::make();
    }
}
