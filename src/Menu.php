<?php

namespace AllDressed;

use AllDressed\Builders\MenuBuilder;

class Menu extends Base
{
    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\MenuBuilder
     */
    public static function query(): MenuBuilder
    {
        return MenuBuilder::make();
    }
}
