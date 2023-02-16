<?php

namespace AllDressed\Builders;

use AllDressed\Client;
use AllDressed\Exceptions\InvalidMenuIdentifierException;
use AllDressed\Exceptions\MissingMenuException;
use AllDressed\Item;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ItemBuilder extends RequestBuilder
{
    /**
     * Set the menu of the query.
     *
     * @param  string  $menu
     * @return static
     */
    public function forMenu(string $menu): static
    {
        $menu = Str::of($menu)->when(
            Str::isUuid($menu) === false,
            static fn ($string) => $string->match('/^\d{4}-\d{2}-\d{2}$/')
        );

        throw_if($menu->isEmpty(), InvalidMenuIdentifierException::class);

        return $this->withOption('menu', $menu->toString());
    }

    /**
     * Retrieve the items.
     *
     * @return \Illuminate\Support\Collection<int, \AllDressed\Item>
     */
    public function get(): Collection
    {
        throw_unless(
            $menu = $this->getOption('menu'),
            MissingMenuException::class
        );

        $client = resolve(Client::class);

        $endpoint = "menus/{$menu}/items";

        try {
            $response = $client->get($endpoint, array_filter([
                'types' => implode(
                    ',',
                    array_keys(Arr::wrap($this->getOption('types')))
                ),
            ]));

            Log::debug($response->body());
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
            );
        }

        $data = $response->json('data');

        return collect($data)->mapInto(Item::class);
    }

    /**
     * Filter the items that are packages.
     *
     * @return static
     */
    public function packages(): static
    {
        return $this->withOption('types', array_merge(
            Arr::wrap($this->getOption('types')),
            [
                'package' => true,
            ]
        ));
    }

    /**
     * Filter the items that are products.
     *
     * @return static
     */
    public function products(): static
    {
        return $this->withOption('types', array_merge(
            Arr::wrap($this->getOption('types')),
            [
                'product' => true,
            ]
        ));
    }

    /**
     * Throw a new friendly exception based on the existing exception.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    protected function throw(Throwable $exception): void
    {
        throw $exception;
    }
}
