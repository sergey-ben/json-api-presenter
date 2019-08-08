<?php


namespace JsonApiPresenter\Contracts;


use JsonApiPresenter\ResourceObjectsCollection;

interface DataSourceInterface
{

    /**
     * @param string ...$ids
     * @return ResourceObjectsCollection
     */
    public function havingIds(string ...$ids): ResourceObjectsCollection;

}