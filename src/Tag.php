<?php

namespace AllDressed;

use AllDressed\Builders\TagBuilder;

class Tag extends Base
{
    /*
     * Create a new query builder.
     */
    public static function query(): TagBuilder
    {
        return TagBuilder::make();
    }
}
