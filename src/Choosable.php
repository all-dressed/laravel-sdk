<?php

namespace AllDressed;

use Illuminate\Support\Fluent;

class Choosable extends Fluent
{
    /**
     * The attributes that are mass assignable.
     *
     * * @var array<int, string>
     */
    protected $fillable = [
        'id',
    ];

    /**
     * Create a new instance.
     *
     * @return static
     */
    public static function make(...$args): static
    {
        return new static(...$args);
    }
}
