<?php

namespace AllDressed;

use AllDressed\Builders\MenuBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class Menu extends Base
{
    /**
     * Create a new menu instance.
     *
     * @param  iterable<TKey, TValue>  $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        if ($from = Arr::get($attributes, 'from')) {
            Arr::set($attributes, 'from', Carbon::parse($from));
        }

        if ($to = Arr::get($attributes, 'to')) {
            Arr::set($attributes, 'to', Carbon::parse($to));
        }

        parent::__construct($attributes);
    }

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
