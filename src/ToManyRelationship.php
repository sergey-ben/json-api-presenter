<?php


namespace JsonApiPresenter;


use JsonApiPresenter\Contracts\Relationship;
use JsonApiPresenter\Exceptions\InvalidArgumentException;

final class ToManyRelationship implements Relationship
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var array|ResourceIdentifier[]
     */
    private $data = [];

    /**
     * @var ResourceLinks|null
     */
    private $links;

    /**
     * @var Meta|null
     */
    private $meta;

    /**
     * ToManyRelationship constructor.
     * @param string $name
     * @param ResourceIdentifier[] $data
     * @param ResourceLinks|null $links
     * @param Meta|null $meta
     * @throws InvalidArgumentException
     */
    public function __construct(
        string $name,
        array $data = [],
        ResourceLinks $links = null,
        Meta $meta = null
    ) {
        if (empty($name)) {
            throw new InvalidArgumentException('Relationship name can\'t be empty');
        }

        foreach ($data as $identifier) {
            $this->add($identifier);
        }

        $this->name = $name;
        $this->links = $links;
        $this->meta = $meta;
    }

    /**
     * @param ResourceIdentifier $identifier
     */
    public function add(ResourceIdentifier $identifier)
    {
        $this->data[] = $identifier;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array|ResourceIdentifier[]
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return reset($this->data)->getType();
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->data);
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

        if (null !== $this->links) {
            $result['links'] = $this->links->toArray();
        }

        if (null !== $this->meta) {
            $result['meta'] = $this->meta->toArray();
        }

        return $result;
    }
}