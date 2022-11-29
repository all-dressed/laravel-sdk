<?php

namespace AllDressed;

use AllDressed\Builders\PackageBuilder;

class Package extends Base
{
    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\PackageBuilder
     */
    public static function query(): PackageBuilder
    {
        return PackageBuilder::make();
    }
}
