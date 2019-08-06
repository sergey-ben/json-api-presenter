<?php


namespace JsonApiPresenter\Contracts;


use JsonApiPresenter\PaginationLinks;
use JsonApiPresenter\Requests\FiltersCollection;
use JsonApiPresenter\Requests\Pagination;
use JsonApiPresenter\Requests\SortingCollection;

interface ProvidesPaginationLinksDataSourceInterface
{

    /**
     * @param FiltersCollection $filters
     * @param SortingCollection $sorting
     * @param Pagination $pagination
     * @return PaginationLinks
     */
    public function providePaginationLinks(
        FiltersCollection $filters,
        SortingCollection $sorting,
        Pagination $pagination
    ): PaginationLinks;

}