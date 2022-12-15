<?php

namespace AllDressed;

use AllDressed\Builders\PackageBuilder;
use Illuminate\Support\Arr;

class Package extends Base
{
    /**
     * Create a new package instance.
     *
     * @param  iterable<TKey, TValue>  $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        if ($packages = Arr::get($attributes, 'packages')) {
            $attributes['packages'] = collect($packages)->mapInto(static::class);
        }

        parent::__construct($attributes);
    }

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
