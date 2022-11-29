<?php

namespace AllDressed\Laravel;

use AllDressed\Concerns\ForwardsToBuilder;
use Illuminate\Support\Fluent;

abstract class Base extends Fluent
{
    use ForwardsToBuilder;

    /**
     * Alias of the constructor.
     *
     * @param  mixed  $args
     * @return static
     */
    public static function make(...$args): static
    {
        return new static(...$args);
    }
}
