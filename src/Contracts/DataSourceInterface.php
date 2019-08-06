<?php


namespace JsonApiPresenter\Contracts;


use JsonApiPresenter\ResourceObject;

interface DataSourceInterface
{

    /**
     * @param string ...$ids
     * @return ResourceObject[]
     */
    public function havingIds(string ...$ids): array;

}