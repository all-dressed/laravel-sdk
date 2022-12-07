<?php

namespace AllDressed;

use AllDressed\Builders\Builder;
use AllDressed\Concerns\ForwardsToBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

abstract class Base extends Fluent
{
    use ForwardsToBuilder;

    /**
     * Retrieve all the instances.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function all(): Collection
    {
        return static::query()->get();
    }

    /**
     * Retrieve the builder for the instance.
     *
     * @return \AllDressed\Builders\Builder
     */
    abstract public static function query(): Builder;
}
