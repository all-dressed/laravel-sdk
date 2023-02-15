<?php

namespace AllDressed\Builders;

use AllDressed\Exceptions\NotImplementedException;
use Illuminate\Support\Collection;
use Throwable;

class ChoiceBuilder extends Builder
{
    /**
     * Retrieve the items.
     *
     * @return \Illuminate\Support\Collection<int, \AllDressed\Choice>
     */
    public function get(): Collection
    {
        throw new NotImplementedException;
    }

    /**
     * Throw a new friendly exception based on the existing exception.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    protected function throw(Throwable $exception): void
    {
        throw $exception;
    }
}
