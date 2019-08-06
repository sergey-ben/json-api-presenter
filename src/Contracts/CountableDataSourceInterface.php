<?php


namespace JsonApiPresenter\Contracts;


use JsonApiPresenter\Requests\FiltersCollection;

interface CountableDataSourceInterface
{

    /**
     * @param FiltersCollection $filters
     * @return int
     */
    public function count(FiltersCollection $filters): int;

}