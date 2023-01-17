<?php

namespace AllDressed\Concerns;

use Illuminate\Support\Traits\ForwardsCalls;

trait ForwardsToBuilder
{
    use ForwardsCalls {
        ForwardsCalls::forwardCallTo as baseForward;
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this, $method, $parameters);
    }

    /**
     * Handle dynamic static method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return (new static)->{$method}(...$parameters);
    }

    /**
     * Forward a method call to the given object.
     *
     * @param  mixed  $object
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    protected function forwardCallTo($object, $method, $parameters)
    {
        $builder = $object::query();

        if (method_exists($builder, $method)) {
            return $builder->{$method}(...$parameters);
        }

        return $this->baseForward($object, $method, $parameters);
    }
}
