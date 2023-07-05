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
    public function __construct(Throwable $throwable, string $code)
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
