<?php

namespace AllDressed\Builders;

use AllDressed\Client;
use AllDressed\Tag;
use Illuminate\Support\Collection;

class TagBuilder extends RequestBuilder
{
    /**
     * Retrieve the tags.
     *
     * @return \Illuminate\Support\Collection<int, \AllDressed\Tag>
     */
    public function get(): Collection
    {
        $client = resolve(Client::class);

        $endpoint = 'tags';

        if ($id = $this->getOption('id')) {
            $endpoint = "{$endpoint}/{$id}";
        }

        $response = $client->get($endpoint);

        $data = $response->json('data');

        if ($id) {
            $data = [$data];
        }

        return collect($data)->mapInto(Tag::class);
    }
}
