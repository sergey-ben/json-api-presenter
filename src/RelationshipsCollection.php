<?php


namespace JsonApiPresenter;


use JsonApiPresenter\Contracts\Arrayable;
use JsonApiPresenter\Contracts\Relationship;
use JsonApiPresenter\Exceptions\InvalidArgumentException;
use JsonApiPresenter\Exceptions\RelationshipNotFoundException;
use Traversable;

class RelationshipsCollection implements Arrayable, \IteratorAggregate
{

    /**
     * @var Relationship[]
     */
    private $relationships = [];

    /**
     * Relationships constructor.
     * @param Relationship ...$relationships
     */
    public function __construct(Relationship ...$relationships)
    {
        foreach ($relationships as $relationship) {
            $this->addRelationship($relationship);
        }
    }

    /**
     * @param string $name
     * @param ResourceIdentifier|null $data
     * @param ResourceLinks|null $links
     * @param Meta|null $meta
     * @throws InvalidArgumentException
     */
    public function addToOneRelationship(
        string $name,
        ResourceIdentifier $data = null,
        ResourceLinks $links = null,
        Meta $meta = null
    ) {
        $this->addRelationship(new ToOneRelationship($name, $data, $links, $meta));
    }

    /**
     * @param string $name
     * @param array $data
     * @param ResourceLinks|null $links
     * @param Meta|null $meta
     * @throws InvalidArgumentException
     */
    public function addToManyRelationship(
        string $name,
        array $data = [],
        ResourceLinks $links = null,
        Meta $meta = null
    ) {
        $this->addRelationship(new ToManyRelationship($name, $data, $links, $meta));
    }

    /**
     * @param string $name
     * @return Relationship
     * @throws RelationshipNotFoundException
     */
    public function getRelationship(string $name): Relationship
    {
        if (!$this->hasRelationship($name)) {
            throw new RelationshipNotFoundException();
        }

        return $this->relationships[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasRelationship(string $name): bool
    {
        return isset($this->relationships[$name]);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->relationships);
    }

    /**
     * @param Relationship $relationship
     */
    private function addRelationship(Relationship $relationship)
    {
        $this->relationships[$relationship->getName()] = $relationship;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $result = [];

        foreach ($this->relationships as $relationship) {
            $result[$relationship->getName()] = $relationship->toArray();
        }

        return $result;
    }

    /**
     * @return \ArrayIterator|Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->relationships);
    }

}