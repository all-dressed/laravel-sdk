<?php

namespace AllDressed;

use AllDressed\Builders\DeliveryScheduleBuilder;
use Illuminate\Support\Collection;

class DeliverySchedule extends Base
{
    /**
     * Retrieve all the zones.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function all(): Collection
    {
        return DeliveryScheduleBuilder::make()->get();
    }

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
