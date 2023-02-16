<?php

namespace AllDressed\Builders;

abstract class Builder
{
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
