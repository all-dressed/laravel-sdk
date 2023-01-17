<?php

namespace AllDressed;

use AllDressed\Builders\ProductBuilder;

class Product extends Base
{
    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\ProductBuilder
     */
    public static function query(): ProductBuilder
    {
        return ProductBuilder::make();
    }
}
