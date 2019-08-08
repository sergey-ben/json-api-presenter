<?php


namespace JsonApiPresenter\Contracts;


use JsonApiPresenter\Requests\FiltersCollection;
use JsonApiPresenter\Requests\Pagination;
use JsonApiPresenter\Requests\SortingCollection;
use JsonApiPresenter\ResourceObjectsCollection;

interface QueryableDataSourceInterface extends DataSourceInterface
{

    /**
     * @param FiltersCollection $filters
     * @param SortingCollection $sorting
     * @param Pagination $pagination ,
     * @return ResourceObjectsCollection
     */
    public function fetch(
        FiltersCollection $filters,
        SortingCollection $sorting,
        Pagination $pagination
    ): ResourceObjectsCollection;

}