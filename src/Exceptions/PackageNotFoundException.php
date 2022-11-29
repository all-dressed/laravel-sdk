<?php

namespace AllDressed\Exceptions;

use Exception;
use Throwable;

class PackageNotFoundException extends Exception
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
            __('Package with id :id not found.', [
                'id' => $id,
            ]),
            404,
            $throwable
        );
    }
}
