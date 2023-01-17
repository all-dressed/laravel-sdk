<?php

namespace AllDressed;

use AllDressed\Builders\ProductBuilder;
use Illuminate\Support\Arr;

class Product extends Base
{
    /**
     * Create a new package instance.
     *
     * @param  iterable<TKey, TValue>  $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        $attributes['prices'] = collect(Arr::get($attributes, 'prices', []))
            ->mapInto(Price::class);

        parent::__construct($attributes);
    }

    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\ProductBuilder
     */
    public static function query(): ProductBuilder
    {
        return ProductBuilder::make();
    }
}
