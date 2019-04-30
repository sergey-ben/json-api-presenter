<?php


namespace JsonApiPresenter\Contracts;


use JsonApiPresenter\Exceptions\RuntimeException;
use JsonApiPresenter\ResourceObject;

interface ResourceCacheInterface
{

    /**
     * @param string $key
     * @param ResourceObject $resource
     * @return mixed
     */
    public function add(string $key, ResourceObject $resource);

    /**
     * @param string $key
     * @return ResourceObject
     * @throws RuntimeException
     */
    public function get(string $key): ResourceObject;

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

}