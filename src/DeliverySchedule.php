<?php

namespace AllDressed;

use AllDressed\Builders\DeliveryScheduleBuilder;

class DeliverySchedule extends Base
{
    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\DeliveryScheduleBuilder
     */
    public static function query(): DeliveryScheduleBuilder
    {
        return DeliveryScheduleBuilder::make();
    }
}
