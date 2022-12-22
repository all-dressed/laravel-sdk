<?php

namespace AllDressed;

use AllDressed\Builders\ItemBuilder;

class Item extends Base
{
    /**
     * Create a new package instance.
     *
     * @param  iterable<TKey, TValue>  $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\ItemBuilder
     */
    public static function query(): ItemBuilder
    {
        return ItemBuilder::make();
    }
}
