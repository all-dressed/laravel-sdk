<?php

namespace AllDressed;

use AllDressed\Builders\ZoneBuilder;

class Zone extends Base
{
    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\ZoneBuilder
     */
    public static function query(): ZoneBuilder
    {
        return ZoneBuilder::make();
    }
}
