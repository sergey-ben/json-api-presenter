<?php


namespace JsonApiPresenter;


use JsonApiPresenter\Contracts\Relationship;
use JsonApiPresenter\Exceptions\InvalidArgumentException;

final class ToOneRelationship implements Relationship
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var ResourceIdentifier|null
     */
    private $data;

    /**
     * @var ResourceLinks|null
     */
    private $links;

    /**
     * @var Meta|null
     */
    private $meta;

    /**
     * ToOneRelationship constructor.
     * @param string $name
     * @param ResourceIdentifier|null $data
     * @param ResourceLinks|null $links
     * @param Meta|null $meta
     * @throws InvalidArgumentException
     */
    public function __construct(
        string $name,
        ResourceIdentifier $data = null,
        ResourceLinks $links = null,
        Meta $meta = null
    ) {
        if (empty($name)) {
            throw new InvalidArgumentException('Relationship name can\'t be empty');
        }

        $this->name = $name;
        $this->data = $data;
        $this->links = $links;
        $this->meta = $meta;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return ResourceIdentifier|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return ResourceLinks|null
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @return Meta|null
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return null === $this->data;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $result = [
            'data' => null
        ];

        if (null !== $this->data) {
            $result['data'][] = $this->data->toArray();
        }

        if (null !== $this->links) {
            $result['links'] = $this->links->toArray();
        }

        if (null !== $this->meta) {
            $result['meta'] = $this->meta->toArray();
        }

        return $result;
    }

}