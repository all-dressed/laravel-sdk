<?php

namespace AllDressed;

use AllDressed\Concerns\ForwardsToBuilder;
use Illuminate\Support\Fluent;

abstract class Base extends Fluent
{
    use ForwardsToBuilder;
}
