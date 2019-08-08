<?php


namespace JsonApiPresenter;


use JsonApiPresenter\Exceptions\RuntimeException;
use Traversable;

class ResourceObjectsCollection implements \IteratorAggregate, \Countable
{

    /**
     * @var ResourceObject[]
     */
    private $collection = [];

    /**
     * ResourceObjectsCollection constructor.
     * @param ResourceObject ...$collection
     */
    public function __construct(ResourceObject ...$collection)
    {
        foreach ($collection as $item) {
            $this->collection[$item->getIdentifier()->getId()] = $item;
        }
    }

    /**
     * @param string[] $names
     * @throws Exceptions\InvalidArgumentException
     */
    public function defineToManyRelationships(string ...$names)
    {
        foreach ($names  as $name) {
            $this->defineToManyRelationship($name);
        }
    }

    /**
     * @param string[] $names
     * @throws Exceptions\InvalidArgumentException
     */
    public function defineToOneRelationships(string ...$names)
    {
        foreach ($names  as $name) {
            $this->defineToOneRelationship($name);
        }
    }

    /**
     * @param string $name
     * @throws Exceptions\InvalidArgumentException
     */
    public function defineToManyRelationship(string $name)
    {
        foreach ($this->collection as $resource) {
            $resource->addToManyRelationship($name, []);
        }
    }

    /**
     * @param string $name
     * @throws Exceptions\InvalidArgumentException
     */
    public function defineToOneRelationship(string $name)
    {
        foreach ($this->collection as $resource) {
            $resource->addToOneRelationship($name);
        }
    }

    /**
     * @return array|string[]
     */
    public function getResourcesIds(): array
    {
        return \array_map(function (ResourceObject $resource) {
            return $resource->getIdentifier()->getId();
        }, $this->collection);
    }

    /**
     * @param string $id
     * @return ResourceObject
     * @throws RuntimeException
     */
    public function getById(string $id): ResourceObject
    {
        if (!isset($this->collection[$id])) {
            throw new RuntimeException('');
        }

        return $this->collection[$id];
    }

    /**
     * @return \ArrayIterator|Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator(\array_values($this->collection));
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->collection);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return \array_values($this->collection);
    }

}