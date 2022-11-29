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
     * Retrieve the instance by it's id.
     *
     * @param  string  $id
     * @return static
     */
    public static function find(string $id): static
    {
        return static::query()->withOption('id', $id)->first();
    }

    /**
     * Retrieve the builder for the instance.
     *
     * @return \AllDressed\Builders\Builder
     */
    abstract public static function query(): Builder;
}
