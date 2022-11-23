<?php

namespace AllDressed\Laravel;

use AllDressed\Laravel\Concerns\ForwardsToBuilder;
use Illuminate\Support\Fluent;

abstract class Base extends Fluent
{
    use ForwardsToBuilder;
}
