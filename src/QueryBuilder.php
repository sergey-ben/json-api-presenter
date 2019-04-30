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
     * @var RequestIncludes
     */
    private $includes;

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
        $this->includes = new RequestIncludes();
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
        $this->includes = new RequestIncludes($includes);

        return $this;
    }

    /**
     * @param Meta|null $meta
     * @param ResourceLinks|null $links
     * @param JsonApi|null $jsonApi
     * @return Document
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\ResourceRepositoryNotFoundException
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
        $includes = $this->getIncludesForResource($resource);
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
     * @throws Exceptions\ResourceRepositoryNotFoundException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws RuntimeException
     */
    public function getResult(Meta $meta = null, ResourceLinks $links = null, JsonApi $jsonApi = null): Collection
    {
        $resources = $this->getResourcesCollection(...\array_map(function(string $id) {
            return new ResourceIdentifier($id, $this->resourceType);
        }, $this->ids));

        $includes = $this->getIncludesForResources(...$resources);
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
     * @param ResourceObject ...$resources
     * @return array
     * @throws Exceptions\InvalidArgumentException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws RuntimeException
     * @throws Exceptions\ResourceRepositoryNotFoundException
     */
    private function getIncludesForResources(ResourceObject ...$resources): array
    {
        $result = [];

        foreach ($resources as $resource) {
            $result = \array_merge($result, $this->getIncludesForResource($resource));
        }

        return $result;
    }

    /**
     * @param ResourceObject $resource
     * @return array
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\ResourceRepositoryNotFoundException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws RuntimeException
     */
    private function getIncludesForResource(ResourceObject $resource): array
    {
        $relationships = $resource->getRelationships();
        $result = [];

        foreach ($relationships as $relationship) {
            if (!$this->includes->hasInclude($relationship->getName())) {
                continue ;
            }

            if ($relationship->isEmpty()) {
                continue ;
            }

            if ($relationship instanceof ToOneRelationship) {
                $result = \array_merge($result, $this->getToOneRelationshipIncludes($relationship));

                continue ;
            }

            if ($relationship instanceof ToManyRelationship) {
                $result = \array_merge($result, $this->getToManyRelationshipIncludes($relationship));

                continue ;
            }
        }

        return $result;
    }

    /**
     * @param ToOneRelationship $relationship
     * @return array
     * @throws Exceptions\InvalidArgumentException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws RuntimeException
     * @throws Exceptions\ResourceRepositoryNotFoundException
     */
    private function getToOneRelationshipIncludes(ToOneRelationship $relationship): array
    {
        $document = $this
            ->resourceManager
            ->createQueryBuilder()
            ->select($relationship->getData()->getType())
            ->withId($relationship->getData()->getId())
            ->include(...$this->includes->includesOf($relationship->getName())->toArray())
            ->getSingleResult();

        $result = [$document->getData()];

        if ($document->hasIncludes()) {
            $result = \array_merge($document->getIncludes());
        }

        return $result;
    }

    /**
     * @param ToManyRelationship $relationship
     * @return array
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\ResourceRepositoryNotFoundException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws RuntimeException
     */
    private function getToManyRelationshipIncludes(ToManyRelationship $relationship): array
    {
        $type = $relationship->getType();
        $ids = \array_map(function (ResourceIdentifier $identifier) {
            return $identifier->getId();
        }, $relationship->getData());

        $collection = $this
            ->resourceManager
            ->createQueryBuilder()
            ->select($type)
            ->withIds(...$ids)
            ->include(...$this->includes->includesOf($relationship->getName())->toArray())
            ->getResult();

        $result = $collection->getData();

        if ($collection->hasIncludes()) {
            $result = \array_merge($collection->getIncludes());
        }

        return $result;
    }

    /**
     * @param ResourceIdentifier $identity
     * @return ResourceObject
     * @throws Exceptions\ResourceRepositoryNotFoundException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws RuntimeException
     */
    private function getSingleResource(ResourceIdentifier $identity): ResourceObject
    {
        if ($this->cache->has((string) $identity)) {
            return $this->cache->get((string) $identity);
        }

        $repository = $this->resourceManager->repositoryFor(
            $this->resourceType
        );

        $resources = $repository->havingIds($identity->getId());

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
     * @throws Exceptions\ResourceRepositoryNotFoundException
     * @throws RuntimeException
     */
    private function getResourcesCollection(ResourceIdentifier ...$identifiers)
    {
        $result = [];
        $ids = [];

        foreach ($identifiers as $identifier) {
            if ($this->cache->has((string) $identifier)) {
                $result[] = $this->cache->get((string) $identifier);

                continue ;
            }

            $ids[] = $identifier->getId();
        }

        $repository = $this->resourceManager->repositoryFor(
            $this->resourceType
        );

        $resources = $repository->havingIds(...$ids);

        foreach ($resources as $resource) {
            $this->cache->add(
                (string) $resource->getIdentifier(),
                $resource
            );
        }

        return \array_merge($result, $resources);
    }

}