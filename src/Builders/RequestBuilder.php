<?php

namespace AllDressed\Builders;

use AllDressed\Base;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

abstract class RequestBuilder extends Builder
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
     * @return \Illuminate\Support\Collection<int, \AllDressed\Base>
     */
    public function all(): Collection
    {
        return $this->get();
    }

    /**
     * Retrieve the instance by it's uuid.
     *
     * @param  string  $id
     * @return \AllDressed\Base|null
     */
    public function find(string $id): ?Base
    {
        return $this->withOption('id', $id)->first();
    }

    /**
     * Retrieve the first instance from the response.
     *
     * @return \AllDressed\Base|null
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
