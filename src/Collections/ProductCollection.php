<?php

namespace AllDressed\Collections;

class ProductCollection extends Collection
{
    /**
     * Convert the products to a payload.
     */
    public function toPayload(): array
    {
        return $this->map(static function ($product) {
            return [
                'id' => $product->id,
                'quantity' => $product->quantity ?? 1,
            ];
        })->all();
    }
}
