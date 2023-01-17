<?php

namespace AllDressed;

use AllDressed\Builders\PackageBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

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
        $attributes['packages'] = collect(Arr::get($attributes, 'packages'))
            ->mapInto(static::class);

        $attributes['prices'] = collect(Arr::get($attributes, 'prices', []))
            ->mapInto(Price::class);

        parent::__construct($attributes);
    }

    /**
     * Retrieve the products of the package.
     *
     * @return \Illuminate\Support\Collection<int, \AllDressed\Product>
     */
    public function getProducts(): Collection
    {
        if ($this->missingAttribute('products')) {
            $this->products = Product::query()->forPackage($this->id)->get();
        }

        return $this->products;
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
