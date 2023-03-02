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

        if ($cutOff = Arr::get($attributes, 'cut_off')) {
            Arr::set($attributes, 'cut_off', Carbon::parse($cutOff));
        }

        if ($delivery = Arr::get($attributes, 'delivery')) {
            Arr::set($attributes, 'delivery', Carbon::parse($delivery));
        }

        if ($to = Arr::get($attributes, 'to')) {
            Arr::set($attributes, 'to', Carbon::parse($to));
        }

        parent::__construct($attributes);
    }

    /**
     * Create a instance of the menu for the given date.
     *
     * @param  \Illuminate\Support\Carbon|string  $date
     * @return static
     */
    public static function for(Carbon|string $date): static
    {
        return static::make([
            'id' => Carbon::parse($date)->format('Y-m-d'),
        ]);
    }

    /**
     * Create a instance of the menu for the current date.
     *
     * @return static
     */
    public static function now(): static
    {
        return static::for(now());
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
