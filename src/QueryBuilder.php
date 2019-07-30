<?php


namespace JsonApiPresenter;


use JsonApiPresenter\Contracts\ResourceCacheInterface;
use JsonApiPresenter\Contracts\ResourceObjectInterface;
use JsonApiPresenter\Exceptions\NonUniqueResultException;
use JsonApiPresenter\Exceptions\NoResultException;
use JsonApiPresenter\Exceptions\RuntimeException;

class QueryBuilder
{

    /**
     * @var ResourceManager
     */
    private $resourceManager;

    /**
     * @var string|null
     */
    private $resourceType;

    /**
     * @var string[]|null
     */
    private $ids = [];

    /**
     * @var string[]
     */
    private $includes;

    /**
     * @var string
     */
    private $delimiter = IncludesRequest::DEFAULT_DELIMITER;

    /**
     * @var ResourceCacheInterface
     */
    private $cache;

    /**
     * QueryBuilder constructor.
     * @param ResourceManager $resourceManager
     * @param ResourceCacheInterface $cache
     */
    public function __construct(ResourceManager $resourceManager, ResourceCacheInterface $cache)
    {
        $this->resourceManager = $resourceManager;
        $this->cache = $cache;
        $this->includes = new IncludesRequest();
    }

    /**
     * @param string $resourceType
     * @return QueryBuilder
     */
    public function select(string $resourceType): QueryBuilder
    {
        $this->resourceType = $resourceType;

        return $this;
    }

    /**
     * @param string $id
     * @return QueryBuilder
     */
    public function withId(string $id): QueryBuilder
    {
        $this->ids = [$id];

        return $this;
    }

    /**
     * @param string ...$ids
     * @return QueryBuilder
     */
    public function withIds(string ...$ids): QueryBuilder
    {
        $this->ids = $ids;

        return $this;
    }

    /**
     * @param string ...$includes
     * @return QueryBuilder
     */
    public function include(string ...$includes): QueryBuilder
    {
        $this->includes = $includes;

        return $this;
    }

    /**
     * @param string $delimiter
     * @return QueryBuilder
     */
    public function includesDelimiter(string $delimiter): QueryBuilder
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * @param Meta|null $meta
     * @param ResourceLinks|null $links
     * @param JsonApi|null $jsonApi
     * @return Document
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataSourceNotFoundException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws RuntimeException
     */
    public function getSingleResult(Meta $meta = null, ResourceLinks $links = null, JsonApi $jsonApi = null): Document
    {
        if (\count($this->ids) != 1) {
            throw new RuntimeException();
        }

        $identity = new ResourceIdentifier(
            \reset($this->ids),
            $this->resourceType
        );

        $resource = $this->getSingleResource($identity);
        $includes = $this->getIncludesForResource($this->getIncludesRequest(), $resource);
        $jsonApi = $jsonApi ?? JsonApi::default();

        return new Document(
            $resource,
            $meta,
            $links,
            $jsonApi,
            ...$includes
        );
    }

    /**
     * @param Meta|null $meta
     * @param ResourceLinks|null $links
     * @param JsonApi|null $jsonApi
     * @return Collection
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataSourceNotFoundException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws RuntimeException
     */
    public function getResult(Meta $meta = null, ResourceLinks $links = null, JsonApi $jsonApi = null): Collection
    {
        $resources = $this->getResourcesCollection(...\array_map(function(string $id) {
            return new ResourceIdentifier($id, $this->resourceType);
        }, $this->ids));

        $includes = $this->getIncludesForResources($this->getIncludesRequest(), ...$resources);
        $jsonApi = $jsonApi ?? JsonApi::default();

        return new Collection(
            $resources,
            $meta,
            $links,
            null,
            $jsonApi,
            ...$includes
        );
    }

    /**
     * @param IncludesRequest $includes
     * @param ResourceObject ...$resources
     * @return array
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataSourceNotFoundException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws RuntimeException
     */
    private function getIncludesForResources(IncludesRequest $includes, ResourceObject ...$resources): array
    {
        $result = [];

        foreach ($resources as $resource) {
            $result = \array_merge($result, $this->getIncludesForResource($includes, $resource));
        }

        return $result;
    }

    /**
     * @param IncludesRequest $includes
     * @param ResourceObject $resource
     * @return array
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataSourceNotFoundException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws RuntimeException
     */
    private function getIncludesForResource(IncludesRequest $includes, ResourceObject $resource): array
    {
        $relationships = $resource->getRelationships();
        $result = [];

        foreach ($relationships as $relationship) {
            if (!$includes->hasInclude($relationship->getName())) {
                continue ;
            }

            if ($relationship->isEmpty()) {
                continue ;
            }

            if ($relationship instanceof ToOneRelationship) {
                $result = \array_merge($result, $this->getToOneRelationshipIncludes($includes, $relationship));

                continue ;
            }

            if ($relationship instanceof ToManyRelationship) {
                $result = \array_merge($result, $this->getToManyRelationshipIncludes($includes, $relationship));

                continue ;
            }
        }

        return $result;
    }

    /**
     * @param IncludesRequest $includes
     * @param ToOneRelationship $relationship
     * @return array
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataSourceNotFoundException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws RuntimeException
     */
    private function getToOneRelationshipIncludes(IncludesRequest $includes, ToOneRelationship $relationship): array
    {
        $resource = $this->getSingleResource($relationship->getData());
        $includes = $this->getIncludesForResource(
            $includes->includesOf($relationship->getName()),
            $resource
        );

        return \array_merge([$resource], $includes);
    }

    /**
     * @param IncludesRequest $includes
     * @param ToManyRelationship $relationship
     * @return array
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataSourceNotFoundException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws RuntimeException
     */
    private function getToManyRelationshipIncludes(IncludesRequest $includes, ToManyRelationship $relationship): array
    {
        $resources = $this->getResourcesCollection(...$relationship->getData());
        $includes = $this->getIncludesForResources(
            $includes->includesOf($relationship->getName()),
            ...$resources
        );

        return \array_merge($resources, $includes);
    }

    /**
     * @param ResourceIdentifier $identity
     * @return ResourceObject
     * @throws Exceptions\DataSourceNotFoundException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws RuntimeException
     */
    private function getSingleResource(ResourceIdentifier $identity): ResourceObject
    {
        if ($this->cache->has((string) $identity)) {
            return $this->cache->get((string) $identity);
        }

        $dataSource = $this->resourceManager->dataSourceFor(
            $this->resourceType
        );

        $resources = $dataSource->havingIds($identity->getId());

        if (\count($resources) == 0) {
            throw new NoResultException();
        }

        if (\count($resources) > 1) {
            throw new NonUniqueResultException();
        }

        $resource = reset($resources);

        $this->cache->add(
            (string) $resource->getIdentifier(),
            $resource
        );

        return $resource;
    }

    /**
     * @param ResourceIdentifier ...$identifiers
     * @return ResourceObjectInterface[]
     * @throws Exceptions\DataSourceNotFoundException
     * @throws RuntimeException
     */
    private function getResourcesCollection(ResourceIdentifier ...$identifiers)
    {
        $result = [];
        $ids = [];
        $resourceType = null;

        foreach ($identifiers as $identifier) {
            if ($this->cache->has((string) $identifier)) {
                $result[] = $this->cache->get((string) $identifier);

                continue ;
            }

            $resourceType = $identifier->getType();
            $ids[] = $identifier->getId();
        }

        if (null === $resourceType) {
            return $result;
        }

        $dataSource = $this->resourceManager->dataSourceFor(
            $resourceType
        );

        $resources = $dataSource->havingIds(...$ids);

        foreach ($resources as $resource) {
            $this->cache->add(
                (string) $resource->getIdentifier(),
                $resource
            );
        }

        return \array_merge($result, $resources);
    }

    /**
     * @return IncludesRequest
     */
    private function getIncludesRequest(): IncludesRequest
    {
        return new IncludesRequest(
            $this->includes,
            $this->delimiter
        );
    }

}