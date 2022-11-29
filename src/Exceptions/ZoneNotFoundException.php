<?php

namespace AllDressed\Exceptions;

use Exception;
use Throwable;

class ZoneNotFoundException extends Exception
{
    /**
     * Create a new exception.
     *
     * @param  string  $postcode
     * @param  \Throwable  $throwable
     */
    public function __construct(string $postcode, Throwable $throwable)
    {
        parent::__construct(
            __('Zone for :postcode not found.', [
                'postcode' => $postcode,
            ]),
            404,
            $throwable
        );
    }
}
