<?php


namespace JsonApiPresenter;


use JsonApiPresenter\Contracts\ResourceRepositoryInterface;
use JsonApiPresenter\Exceptions\ResourceRepositoryNotFoundException;

class ResourceManager
{
    /**
     * @var array
     */
    private $repositoriesMap = [];

    /**
     * @return QueryBuilder
     */
    public function createQueryBuilder(): QueryBuilder
    {
        return new QueryBuilder($this, new InMemoryResourceCache());
    }

    /**
     * @param string $type
     * @param ResourceRepositoryInterface $repository
     */
    public function register(string $type, ResourceRepositoryInterface $repository)
    {
        $this->repositoriesMap[$type] = $repository;
    }

    /**
     * @param string $type
     * @return ResourceRepositoryInterface
     * @throws ResourceRepositoryNotFoundException
     */
    public function repositoryFor(string $type): ResourceRepositoryInterface
    {
        if (!$this->hasRepositoryFor($type)) {
            throw new ResourceRepositoryNotFoundException('Repository ' . $type . ' not found');
        }

        return $this->repositoriesMap[$type];
    }

    /**
     * @param string $type
     * @return bool
     */
    public function hasRepositoryFor(string $type): bool
    {
        return isset($this->repositoriesMap[$type]);
    }

}