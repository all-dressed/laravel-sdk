<?php

namespace AllDressed;

use AllDressed\Builders\ItemBuilder;
use Illuminate\Support\Arr;

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
        $sellable = Arr::get($attributes, 'sellable', []);
        $type = Arr::get($sellable, 'type');
        $cast = null;

        if ($type == 'product') {
            $cast = Product::class;
        }

        if ($cast !== null) {
            $attributes['sellable'] = new $cast($sellable);
        }

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
