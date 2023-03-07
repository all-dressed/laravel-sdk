<?php

namespace AllDressed\Builders;

use AllDressed\Concerns\Makeable;
use Illuminate\Support\Traits\Conditionable;

abstract class Builder
{
    use Conditionable, Makeable;
}
