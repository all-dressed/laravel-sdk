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

    /**
     * Check if the package has children packages.
     *
     * @return bool
     */
    public function hasPackages(): bool
    {
        return (bool) $this->has_packages;
    }

    /**
     * Check if the package has a parent package.
     *
     * @return bool
     */
    public function hasParent(): bool
    {
        return (bool) $this->has_parent;
    }

    /**
     * Check if it is a root package.
     *
     * @return bool
     */
    public function isRoot(): bool
    {
        return ! $this->hasParent();
    }
}
