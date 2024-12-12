<?php

namespace AllDressed;

use AllDressed\Builders\RequestBuilder;
use AllDressed\Concerns\ForwardsToBuilder;
use AllDressed\Concerns\Makeable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

abstract class Base extends Fluent
{
    use ForwardsToBuilder, Makeable;

    /**
     * Update the given attributes.
     *
     * @param  array<string, mixed>  $attributes
     * @return static
     */
    public function fill(array $attributes): static
    {
        foreach ($attributes as $attribute => $value) {
            $this->{$attribute} = $value;
        }

        return $this;
    }

    /**
     * Create a new instance with the given id.
     *
     * @param  string  $id
     * @return static
     */
    public static function for(string $id): static
    {
        return static::make([
            'id' => $id,
        ]);
    }

    /**
     * Check if the instance has the given attribute.
     *
     * @param  string  $name
     * @return bool
     */
    public function hasAttribute(string $name): bool
    {
        return Arr::has($this->attributes, $name);
    }

    /**
     * Check if the instance is missing the given attribute.
     *
     * @param  string  $name
     * @return bool
     */
    public function missingAttribute(string $name): bool
    {
        return ! $this->hasAttribute($name);
    }

    /**
     * Retrieve the builder for the instance.
     *
     * @return \AllDressed\Builders\Builder
     */
    abstract public static function query(): RequestBuilder;
}