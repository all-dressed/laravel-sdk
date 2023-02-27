<?php

namespace AllDressed\Concerns;

trait Makeable
{
    /**
     * Create a new instance.
     *
     * @param  mixed  $args
     * @return static
     */
    public static function make(...$args): static
    {
        return new static(...$args);
    }
}
