<?php


namespace JsonApiPresenter;


use JsonApiPresenter\Contracts\ResourceObjectInterface;
use JsonApiPresenter\Contracts\TopLevelJsonObject;

final class Collection implements TopLevelJsonObject
{

    /**
     * @var ResourceObjectInterface[]
     */
    private $data;

    /**
     * @var Meta
     */
    private $meta;

    /**
     * @var ResourceLinks
     */
    private $links;

    /**
     * @var PaginationLinks
     */
    private $paginationLinks;

    /**
     * @var JsonApi
     */
    private $jsonApi;

    /**
     * @var ResourceObjectInterface[]
     */
    private $includes;

    public function __construct(
        array $data = [],
        Meta $meta = null,
        ResourceLinks $links = null,
        PaginationLinks $paginationLinks = null,
        JsonApi $jsonApi = null,
        ResourceObjectInterface ...$includes
    ) {
        $this->data = $data;
        $this->meta = $meta;
        $this->links = $links;
        $this->paginationLinks = $paginationLinks;
        $this->jsonApi = $jsonApi;
        $this->includes = $includes;
    }

    /**
     * @return array|ResourceObjectInterface[]
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return ResourceObjectInterface[]
     */
    public function getIncludes(): array
    {
        return $this->includes;
    }

    /**
     * @return bool
     */
    public function hasIncludes(): bool
    {
        return !empty($this->includes);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $result = [
            'data' => []
        ];

        foreach ($this->data as $item) {
            $result['data'][] = $item->toArray();
        }

        if (null !== $this->meta) {
            $result['meta'] = $this->meta->toArray();
        }

        if (null !== $this->links || null !== $this->paginationLinks) {
            $result['links'] = [];

            if (null !== $this->links) {
                $result['links'] = $this->links->toArray();
            }

            if (null !== $this->paginationLinks) {
                $result['links'] += $this->paginationLinks->toArray();
            }
        }

        if (null !== $this->jsonApi) {
            $result['jsonapi'] = $this->jsonApi->toArray();
        }

        if (!empty($this->includes)) {
            $result['includes'] = [];

            foreach ($this->includes as $include) {
                $result['includes'][(string) $include->getIdentifier()] = $include->toArray();
            }

            $result['includes'] = \array_values($result['includes']);
        }

        return $result;
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}