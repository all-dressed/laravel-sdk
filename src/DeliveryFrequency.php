<?php

namespace AllDressed;

use AllDressed\Builders\DeliveryFrequencyBuilder;

class DeliveryFrequency extends Base
{
    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\DeliveryFrequencyBuilder
     */
    public static function query(): DeliveryFrequencyBuilder
    {
        return DeliveryFrequencyBuilder::make();
    }
}
