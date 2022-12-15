<?php

namespace AllDressed\Builders;

use Exception;
use Illuminate\Support\Collection;

class NullBuilder extends Builder
{
    /**
     * Retrieve the list of zones.
     *
     * @return \Illuminate\Support\Collection<int, \AllDressed\Package>
     *
     * @throws \Exception
     */
    public function get(): Collection
    {
        throw new Exception('Builder yet not supported.');
    }
}
