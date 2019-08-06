<?php


namespace JsonApiPresenter;


use JsonApiPresenter\Contracts\AppliesRelationshipsDataSourceInterface;
use JsonApiPresenter\Contracts\CountableDataSourceInterface;
use JsonApiPresenter\Contracts\ProvidesPaginationLinksDataSourceInterface;
use JsonApiPresenter\Contracts\QueryableDataSourceInterface;
use JsonApiPresenter\Contracts\ResourceCacheInterface;
use JsonApiPresenter\Exceptions\NonUniqueResultException;
use JsonApiPresenter\Exceptions\NoResultException;
use JsonApiPresenter\Exceptions\RuntimeException;
use JsonApiPresenter\Requests\FieldsetCollection;
use JsonApiPresenter\Requests\FiltersCollection;
use JsonApiPresenter\Requests\Includes;
use JsonApiPresenter\Requests\Pagination;
use JsonApiPresenter\Requests\Sorting;
use JsonApiPresenter\Requests\SortingCollection;

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
     * @var string|null
     */
    private $id = [];

    /**
     * @var string[]
     */
    private $includes;

    /**
     * @var FieldsetCollection
     */
    private $fieldset;

    /**
     * @var FiltersCollection
     */
    private $filters;

    /**
     * @var SortingCollection
     */
    private $sorting;

    /**
     * @var int|null
     */
    private $limit;

    /**
     * @var int|null
     */
    private $offset;

    /**
     * @var string
     */
    private $delimiter = Includes::DEFAULT_DELIMITER;

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
        $this->fieldset = new FieldsetCollection();
        $this->filters = new FiltersCollection();
        $this->sorting = new SortingCollection();
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
        $this->id = $id;

        return $this;
    }

    /**
     * @param string $field
     * @param string $operator
     * @param $value
     * @return QueryBuilder
     */
    public function where(string $field, string $operator, $value): QueryBuilder
    {
        $this->filters->add($field, $operator, $value);

        return $this;
    }

    /**
     * @param string $field
     * @param string $direction
     * @return QueryBuilder
     * @throws Exceptions\InvalidArgumentException
     */
    public function orderBy(string $field, string $direction = Sorting::DIRECTION_ASC): QueryBuilder
    {
        $this->sorting->add($field, $direction);

        return $this;
    }

    /**
     * @param int $limit
     * @return QueryBuilder
     */
    public function limit(int $limit): QueryBuilder
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param int $offset
     * @return QueryBuilder
     */
    public function offset(int $offset): QueryBuilder
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @param string $type
     * @param string ...$fields
     * @return QueryBuilder
     */
    public function fieldset(string $type, string ...$fields): QueryBuilder
    {
        $this->fieldset->add($type, ...$fields);

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
        if (null === $this->id) {
            throw new RuntimeException('Unable to fetch resource by id. Id is not set');
        }

        $identity = new ResourceIdentifier(
            $this->id,
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
        if (null === $this->resourceType) {
            throw new RuntimeException('Please specify resource type by calling `select()` method');
        }

        $dataSource = $this->resourceManager->dataSourceFor(
            $this->resourceType
        );

        if (!$dataSource instanceof QueryableDataSourceInterface) {
            throw new RuntimeException(\sprintf('Data source of %s must be queryable', $this->resourceType));
        }

        $pagination = new Pagination(
            $this->limit,
            $this->offset
        );

        $resources = $dataSource->fetch(
            $this->filters,
            $this->sorting,
            $pagination
        );

        $paginationLinks = null;

        if ($dataSource instanceof AppliesRelationshipsDataSourceInterface) {
            $dataSource->applyRelationships(...$resources);
        }

        if ($dataSource instanceof ProvidesPaginationLinksDataSourceInterface) {
            $paginationLinks = $dataSource->providePaginationLinks(
                $this->filters,
                $this->sorting,
                $pagination
            );
        }

        if ($dataSource instanceof CountableDataSourceInterface) {
            $total = $dataSource->count($this->filters);

            $paginationMeta = new Meta([
                'total' => $total,
                'limit' => $pagination->getLimit(),
                'offset' => $pagination->getOffset(),
            ]);

            $meta = $paginationMeta->merge($meta);
        }

        $this->addFieldsetToResourceAttributes($resources);

        $includes = $this->getIncludesForResources(
            $this->getIncludesRequest(),
            ...$resources
        );

        $jsonApi = $jsonApi ?? JsonApi::default();

        return new Collection(
            $resources,
            $meta,
            $links,
            $paginationLinks,
            $jsonApi,
            ...$includes
        );
    }

    /**
     * @param Includes $includes
     * @param ResourceObject ...$resources
     * @return array
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataSourceNotFoundException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws RuntimeException
     */
    private function getIncludesForResources(Includes $includes, ResourceObject ...$resources): array
    {
        $result = [];

        foreach ($resources as $resource) {
            $result = \array_merge($result, $this->getIncludesForResource($includes, $resource));
        }

        return $result;
    }

    /**
     * @param Includes $includes
     * @param ResourceObject $resource
     * @return array
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataSourceNotFoundException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws RuntimeException
     */
    private function getIncludesForResource(Includes $includes, ResourceObject $resource): array
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
     * @param Includes $includes
     * @param ToOneRelationship $relationship
     * @return array
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataSourceNotFoundException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws RuntimeException
     */
    private function getToOneRelationshipIncludes(Includes $includes, ToOneRelationship $relationship): array
    {
        $resource = $this->getSingleResource($relationship->getData());
        $includes = $this->getIncludesForResource(
            $includes->includesOf($relationship->getName()),
            $resource
        );

        return \array_merge([$resource], $includes);
    }

    /**
     * @param Includes $includes
     * @param ToManyRelationship $relationship
     * @return array
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataSourceNotFoundException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws RuntimeException
     */
    private function getToManyRelationshipIncludes(Includes $includes, ToManyRelationship $relationship): array
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

        if ($dataSource instanceof AppliesRelationshipsDataSourceInterface) {
            $dataSource->applyRelationships($resource);
        }

        $this->cache->add(
            (string) $resource->getIdentifier(),
            $resource
        );

        $this->addFieldsetToResourceAttributes($resource);

        return $resource;
    }

    /**
     * @param ResourceIdentifier ...$identifiers
     * @return ResourceObject[]
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

        if ($dataSource instanceof AppliesRelationshipsDataSourceInterface) {
            $dataSource->applyRelationships(...$resources);
        }

        foreach ($resources as $resource) {
            $this->cache->add(
                (string) $resource->getIdentifier(),
                $resource
            );
        }

        $this->addFieldsetToResourceAttributes(...$resources);

        return \array_merge($result, $resources);
    }

    /**
     * @param ResourceObject ...$resources
     * @throws RuntimeException
     */
    private function addFieldsetToResourceAttributes(ResourceObject ...$resources)
    {
        foreach ($resources as $resource) {
            $type = $resource->getIdentifier()->getType();

            if ($this->fieldset->hasForType($type)) {
                $resource->addFieldset($this->fieldset->getFieldsetForType($type));
            }
        }
    }

    /**
     * @return Includes
     */
    private function getIncludesRequest(): Includes
    {
        return new Includes(
            $this->includes,
            $this->delimiter
        );
    }

}