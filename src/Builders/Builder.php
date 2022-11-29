<?php

namespace AllDressed\Builders;

use AllDressed\Base;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

abstract class Builder
{
    /**
     * The options of the builder.
     *
     * @var array<string, mixed>
     */
    protected $options = [];

    /**
     * Alias of the get method.
     *
     * @return \Illuminate\Support\Collection<int, \AllDressed\Zone>
     */
    public function all(): Collection
    {
        return $this->get();
    }

    /**
     * Retrieve the first instance from the response.
     *
     * @return \AllDressed\Base
     */
    public function first(): ?Base
    {
        return $this->get()->first();
    }

    /**
     * Send the query to the API and return a collection based on the response.
     *
     * @return \Illuminate\Support\Collection
     */
    abstract public function get(): Collection;

    /**
     * Retrieve the value of the given option.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getOption(string $key): mixed
    {
        return Arr::get($this->options, $key);
    }

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

    /**
     * Update an option of the builder.
     *
     * @param  string  $name
     * @param  mixed  $key
     * @return static
     */
    public function withOption(string $name, $value): static
    {
        Arr::set($this->options, $name, $value);

        return $this;
    }
}
