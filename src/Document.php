<?php


namespace JsonApiPresenter;


use JsonApiPresenter\Contracts\ResourceObjectInterface;
use JsonApiPresenter\Contracts\TopLevelJsonObject;

final class Document implements TopLevelJsonObject
{

    /**
     * @var ResourceObjectInterface
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
     * @var JsonApi
     */
    private $jsonApi;

    /**
     * @var ResourceObjectInterface[]
     */
    private $includes;

    public function __construct(
        ResourceObjectInterface $data,
        Meta $meta = null,
        ResourceLinks $links = null,
        JsonApi $jsonApi = null,
        ResourceObjectInterface ...$includes
    ) {
        $this->data = $data;
        $this->meta = $meta;
        $this->links = $links;
        $this->jsonApi = $jsonApi;
        $this->includes = $includes;
    }

    /**
     * @return ResourceObjectInterface
     */
    public function getData(): ResourceObjectInterface
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
            'data' => $this->data->toArray()
        ];

        if (null !== $this->meta) {
            $result['meta'] = $this->meta->toArray();
        }

        if (null !== $this->links) {
            $result['links'] = $this->links->toArray();
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

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}