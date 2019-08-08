<?php


namespace JsonApiPresenter\Requests;


use JsonApiPresenter\Exceptions\RuntimeException;
use Traversable;

class FiltersCollection implements \IteratorAggregate
{

    /**
     * @var Filter[]
     */
    private $filters;

    /**
     * FiltersCollection constructor.
     * @param Filter ...$filters
     */
    public function __construct(Filter ...$filters)
    {
        $this->filters = $filters;
    }

    /**
     * @param string $field
     * @param string $operator
     * @param $value
     */
    public function add(string $field, string $operator, $value)
    {
        $this->filters[] = new Filter(
            $field,
            $operator,
            $value
        );
    }

    /**
     * @param string $field
     * @return bool
     */
    public function hasFilter(string $field): bool
    {
        return null !== $this->findFilter($field);
    }

    /**
     * @param string $field
     * @return Filter
     * @throws RuntimeException
     */
    public function getFilter(string $field): Filter
    {
        $filter = $this->findFilter($field);

        if (null === $filter) {
            throw new RuntimeException(\sprintf('Filter for field %s not found', $field));
        }

        return $filter;
    }

    /**
     * @param string $filter
     * @return mixed
     * @throws RuntimeException
     */
    public function getValueOfFilter(string $filter)
    {
        return $this->getFilter($filter)->getValue();
    }

    /**
     * @return \ArrayIterator|Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->filters);
    }

    /**
     * @param string $field
     * @return Filter|null
     */
    private function findFilter(string $field)
    {
        foreach ($this->filters as $filter) {
            if ($filter->getField() === $field) {
                return $filter;
            }
        }

        return null;
    }

}