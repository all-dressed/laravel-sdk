<?php

namespace AllDressed\Exceptions;

use Exception;
use Throwable;

class DiscountNotFoundException extends Exception
{
    /**
     * Create a new exception.
     *
     * @param  string  $code
     * @param  \Throwable  $throwable
     */
    public function __construct(string $code, Throwable $throwable)
    {
        parent::__construct(
            __('Discount with code :code not found.', [
                'code' => $code,
            ]),
            404,
            $throwable
        );
    }
}
