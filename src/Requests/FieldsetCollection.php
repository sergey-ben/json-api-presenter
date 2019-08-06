<?php


namespace JsonApiPresenter\Requests;


use JsonApiPresenter\Exceptions\RuntimeException;
use Traversable;

class FieldsetCollection implements \IteratorAggregate
{
    /**
     * @var array|Fieldset[]
     */
    private $collection = [];

    /**
     * FieldsetCollection constructor.
     * @param array $collection
     */
    public function __construct(array $collection = [])
    {
        foreach ($collection as $type => $fieldset) {
            $this->add($type, $fieldset);
        }
    }

    /**
     * @param string $type
     * @param string ...$fields
     */
    public function add(string $type, string ...$fields)
    {
        $this->collection[$type] = new Fieldset(
            $type,
            ...$fields
        );
    }

    /**
     * @param string $type
     * @return Fieldset
     * @throws RuntimeException
     */
    public function getFieldsetForType(string $type): Fieldset
    {
        if (!$this->hasForType($type)) {
            throw new RuntimeException(\sprintf('There is no fieldset defined for type %s', $type));
        }

        return $this->collection[$type];
    }

    /**
     * @param string $type
     * @return bool
     */
    public function hasForType(string $type): bool
    {
        return isset($this->collection[$type]);
    }

    /**
     * @return \ArrayIterator|Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->collection);
    }
}