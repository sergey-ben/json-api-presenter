<?php


namespace JsonApiPresenter;


use JsonApiPresenter\Contracts\DataSourceInterface;
use JsonApiPresenter\Exceptions\DataSourceNotFoundException;

class ResourceManager
{
    /**
     * @var array
     */
    private $dataSourceMap = [];

    /**
     * @return QueryBuilder
     */
    public function createQueryBuilder(): QueryBuilder
    {
        return new QueryBuilder($this, new InMemoryResourceCache());
    }

    /**
     * @param string $type
     * @param DataSourceInterface $dataSource
     */
    public function register(string $type, DataSourceInterface $dataSource)
    {
        $this->dataSourceMap[$type] = $dataSource;
    }

    /**
     * @param string $type
     * @return DataSourceInterface
     * @throws DataSourceNotFoundException
     */
    public function dataSourceFor(string $type): DataSourceInterface
    {
        if (!$this->hasDataSourceFor($type)) {
            throw new DataSourceNotFoundException(\sprintf(\sprintf(
                'DataSource for %s not found',
                $type
            )));
        }

        return $this->dataSourceMap[$type];
    }

    /**
     * @param string $type
     * @return bool
     */
    public function hasDataSourceFor(string $type): bool
    {
        return isset($this->dataSourceMap[$type]);
    }

}