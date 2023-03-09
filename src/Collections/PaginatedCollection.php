<?php

namespace AllDressed\Collections;

use AllDressed\Client;
use AllDressed\Exceptions\NoNextPageException;
use AllDressed\Exceptions\NoPreviousPageException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PaginatedCollection extends Collection
{
    /**
     * The links for the pagination.
     *
     * @var array
     */
    protected array $links;

    /**
     * The meta of the pagination.
     *
     * @var array
     */
    protected array $meta;

    /**
     * Build the collection from the given response.
     *
     * @param  \Illuminate\Http\Response  $response
     * @param  string  $class
     * @return static
     */
    public static function fromResponse(Response $response, string $class): static
    {
        $collection = static::make($response->json('data'))->mapInto($class);

        if ($links = $response->json('links')) {
            $collection->setLinks($links);
        }

        if ($meta = $response->json('meta')) {
            $collection->setMeta($meta);
        }

        return $collection;
    }

    /**
     * Retrieve the results from the given pagination link.
     *
     * @param  string  $link
     * @return static
     */
    protected function getResults(string $link): static
    {
        $client = resolve(Client::class);

        for ($i = 0; $i < 5; $i++) {
            $link = Str::after($link, '/');
        }

        parse_str(Str::after($link, '?'), $query);

        return static::fromResponse(
            $client->get($link, $query),
            get_class($this->first())
        );
    }

    /**
     * Retrieve the next page.
     *
     * @return static
     */
    public function next(): static
    {
        $link = Arr::get($this->links, 'next');

        throw_unless($link, NoNextPageException::class);

        return $this->getResults($link);
    }

    /**
     * Retrieve the previous page.
     *
     * @return static
     */
    public function previous(): static
    {
        $link = Arr::get($this->links, 'prev');

        throw_unless($link, NoPreviousPageException::class);

        return $this->getResults($link);
    }

    /**
     * Set the links of the pagination.
     *
     * @param  array  $links
     * @return static
     */
    public function setLinks(array $links): static
    {
        $this->links = $links;

        return $this;
    }

    /**
     * Set the meta of the pagination.
     *
     * @param  array  $meta
     * @return static
     */
    public function setMeta(array $meta): static
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * Retrieve the total number of resources.
     *
     * @return int
     */
    public function total(): int
    {
        return Arr::get($this->meta, 'total', $this->count());
    }

    /**
     * Retrieve the total number of pages.
     *
     * @return int
     */
    public function totalPages(): int
    {
        return Arr::get($this->meta, 'last_page', 1);
    }
}
