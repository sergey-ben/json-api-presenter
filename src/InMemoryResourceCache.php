<?php


namespace JsonApiPresenter;


use JsonApiPresenter\Contracts\ResourceCacheInterface;
use JsonApiPresenter\Exceptions\RuntimeException;

class InMemoryResourceCache implements ResourceCacheInterface
{
    /**
     * @var array
     */
    private $map = [];

    /**
     * @param string $key
     * @param ResourceObject $resource
     */
    public function add(string $key, ResourceObject $resource)
    {
        $this->map[$key] = $resource;
    }

    /**
     * @param string $key
     * @return ResourceObject
     * @throws RuntimeException
     */
    public function get(string $key): ResourceObject
    {
        if (!$this->has($key)) {
            throw new RuntimeException();
        }

        return $this->map[$key];
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->map[$key]);
    }

}