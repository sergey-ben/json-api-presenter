<?php


namespace JsonApiPresenter\Requests;


class Includes
{

    const DEFAULT_DELIMITER = '.';

    /**
     * @var array
     */
    private $includes;

    /**
     * @var string
     */
    private $delimiter;

    /**
     * RequestIncludes constructor.
     * @param string[] $includes
     * @param string $delimiter
     */
    public function __construct(array $includes = [], string $delimiter = self::DEFAULT_DELIMITER)
    {
        $this->includes = $includes;
        $this->delimiter = $delimiter;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->includes;
    }

    /**
     * @param string $name
     * @return Includes
     */
    public function includesOf(string $name): Includes
    {
        $includes = \array_filter($this->toArray(), function(string $include) use ($name) {
            return \strpos($include, $name . $this->delimiter) === 0;
        });

        $includes = \array_map(function(string $include) use ($name) {
            return preg_replace(\sprintf('/%s%s/', $name, $this->delimiter), '', $include, 1);
        }, $includes);

        return new Includes($includes);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasInclude(string $name)
    {
        return \in_array($name, $this->toArray());
    }
}