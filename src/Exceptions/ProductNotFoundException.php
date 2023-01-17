<?php

namespace AllDressed\Exceptions;

use Exception;
use Throwable;

class ProductNotFoundException extends Exception
{
    /**
     * Create a new exception.
     *
     * @param  string  $id
     * @param  \Throwable  $throwable
     */
    public function __construct(string $id, Throwable $throwable)
    {
        parent::__construct(
            __('Product with id :id not found.', [
                'id' => $id,
            ]),
            404,
            $throwable
        );
    }
}
