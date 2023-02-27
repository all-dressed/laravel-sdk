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
}
