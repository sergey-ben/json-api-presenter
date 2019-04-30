<?php


namespace JsonApiPresenter;


class RequestIncludes
{
    const DELIMITER = '.';

    /**
     * @var array
     */
    private $includes;

    /**
     * RequestIncludes constructor.
     * @param array $includes
     */
    public function __construct(array $includes = [])
    {
        $this->includes = $includes;
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
     * @return RequestIncludes
     */
    public function includesOf(string $name): RequestIncludes
    {
        $includes = \array_filter($this->toArray(), function(string $include) use ($name) {
            return \strpos($include, $name . self::DELIMITER) === 0;
        });

        $includes = \array_map(function(string $include) use ($name) {
            return preg_replace(\sprintf('/%s%s/', $name, self::DELIMITER), '', $include, 1);
        }, $includes);

        return new RequestIncludes($includes);
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