<?php

namespace AllDressed;

use AllDressed\Builders\InvoiceBuilder;
use Illuminate\Support\Arr;

class Invoice extends Base
{
    /**
     * Create a new payment method instance.
     *
     * @param  iterable<TKey, TValue>  $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        if (Arr::has($attributes, 'currency')) {
            $attributes['currency'] = Currency::make($attributes['currency']);
        }

        if (Arr::has($attributes, 'lines')) {
            $attributes['lines'] = collect($attributes['lines'])
                ->mapInto(InvoiceLine::class);
        }

        if (Arr::has($attributes, 'order')) {
            $attributes['order'] = Order::make($attributes['order']);
        }

        if (Arr::has($attributes, 'transactions')) {
            $attributes['transactions'] = collect($attributes['transactions'])
                ->mapInto(Transaction::class);
        }

        parent::__construct($attributes);
    }

    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\InvoiceBuilder
     */
    public static function query(): InvoiceBuilder
    {
        return InvoiceBuilder::make();
    }
}
