<?php

namespace AllDressed;

use AllDressed\Concerns\Makeable;
use Illuminate\Support\Fluent;

class Choosable extends Fluent
{
    use Makeable;

    /**
     * The attributes that are mass assignable.
     *
     * * @var array<int, string>
     */
    protected $fillable = [
        'id',
    ];
}
