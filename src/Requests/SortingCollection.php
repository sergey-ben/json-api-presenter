<?php


namespace JsonApiPresenter\Requests;


use JsonApiPresenter\Exceptions\InvalidArgumentException;
use JsonApiPresenter\Exceptions\RuntimeException;
use Traversable;

class SortingCollection implements \IteratorAggregate
{

    /**
     * @var Sorting[]
     */
    private $collection = [];

    /**
     * SortingCollection constructor.
     * @param Sorting ...$collection
     */
    public function __construct(Sorting ...$collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return SortingCollection
     */
    public static function empty(): SortingCollection
    {
        return new self();
    }

    /**
     * @param string $field
     * @param string $direction
     * @throws InvalidArgumentException
     */
    public function add(string $field, string $direction = Sorting::DIRECTION_ASC)
    {
        $this->collection[$field] = new Sorting(
            $field,
            $direction
        );
    }

    /**
     * @param string $field
     * @return bool
     */
    public function hasSortingForField(string $field): bool
    {
        return isset($this->collection[$field]);
    }

    /**
     * @param string $field
     * @return Sorting
     * @throws RuntimeException
     */
    public function getSortingForField(string $field): Sorting
    {
        if (!$this->hasSortingForField($field)) {
            throw new RuntimeException(\sprintf('Sorting for field %s not found', $field));
        }

        return $this->collection[$field];
    }

    /**
     * @return \ArrayIterator|Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->collection);
    }
}