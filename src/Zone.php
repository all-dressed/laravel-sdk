<?php

namespace AllDressed;

use AllDressed\Builders\ZoneBuilder;
use Illuminate\Support\Collection;

class Zone extends Base
{
    /**
     * Retrieve all the zones.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function all(): Collection
    {
        return ZoneBuilder::make()->get();
    }

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
