<?php

namespace AllDressed;

use AllDressed\Builders\NullBuilder;
use Illuminate\Support\Arr;

class InvoiceLine extends Base
{
    /**
     * Create a new payment method instance.
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
        } elseif ($type == 'package') {
            $cast = Package::class;
        }

        if ($cast !== null) {
            $attributes['sellable'] = new $cast($sellable);
        }

        parent::__construct($attributes);
    }

    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\NullBuilder
     */
    public static function query(): NullBuilder
    {
        return NullBuilder::make();
    }
}
