<?php


namespace JsonApiPresenter\Contracts;


use JsonApiPresenter\Requests\FiltersCollection;
use JsonApiPresenter\Requests\Pagination;
use JsonApiPresenter\Requests\SortingCollection;

interface QueryableDataSourceInterface extends DataSourceInterface
{

    /**
     * @param FiltersCollection $filters
     * @param SortingCollection $sorting
     * @param Pagination $pagination,
     * @return array
     */
    public function fetch(
        FiltersCollection $filters,
        SortingCollection $sorting,
        Pagination $pagination
    ): array;

}