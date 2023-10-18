<?php

namespace AllDressed\Exceptions;

use Exception;
use Throwable;

class GiftCardNotFoundException extends Exception
{
    /**
     * Create a new exception.
     */
    public function __construct(string $code, Throwable $throwable)
    {
        parent::__construct(
            __('Gift card with code :code not found.', [
                'code' => $code,
            ]),
            404,
            $throwable
        );
    }
}
