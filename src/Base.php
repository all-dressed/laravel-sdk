<?php

namespace AllDressed;

use AllDressed\Builders\RequestBuilder;
use AllDressed\Concerns\ForwardsToBuilder;
use AllDressed\Concerns\Makeable;
use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use JsonSerializable;

abstract class Base implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    use ForwardsToBuilder, Makeable;

    /**
     * All of the attributes set on the instance.
     */
    protected $attributes = [];

    /**
     * Create a new instance.
     */
    public function __construct($attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }

    /**
     * Handle dynamic calls to the instance to set attributes.
     */
    public function __call($method, $parameters)
    {
        $this->attributes[$method] = count($parameters) > 0 ? reset($parameters) : true;

        return $this;
    }

    /**
     * Dynamically retrieve the value of an attribute.
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Dynamically check if an attribute is set.
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Dynamically set the value of an attribute.
     */
    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * Dynamically unset an attribute.
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }

    /**
     * Retrieve all the instances.
     */
    public static function all(): Collection
    {
        return static::query()->get();
    }

    /**
     * Update the given attributes.
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
     */
    public static function for(string $id): static
    {
        return static::make([
            'id' => $id,
        ]);
    }

    /**
     * Get an attribute from the instance.
     */
    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }

        return value($default);
    }

    /**
     * Get the attributes from the instance.
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Check if the instance has the given attribute.
     */
    public function hasAttribute(string $name): bool
    {
        return Arr::has($this->attributes, $name);
    }

    /**
     * Convert the object into something JSON serializable.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Check if the instance is missing the given attribute.
     */
    public function missingAttribute(string $name): bool
    {
        return ! $this->hasAttribute($name);
    }

    /**
     * Determine if the given offset exists.
     */
    public function offsetExists($offset): bool
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * Get the value for a given offset.
     */
    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Set the value at the given offset.
     */
    public function offsetSet($offset, $value): void
    {
        $this->attributes[$offset] = $value;
    }

    /**
     * Unset the value at the given offset.
     */
    public function offsetUnset($offset): void
    {
        unset($this->attributes[$offset]);
    }

    /**
     * Retrieve the builder for the instance.
     */
    abstract public static function query(): RequestBuilder;

    /**
     * Convert the instance to an array.
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * Convert the instance to JSON.
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }
}
