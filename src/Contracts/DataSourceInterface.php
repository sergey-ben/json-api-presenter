<?php


namespace JsonApiPresenter\Contracts;


interface DataSourceInterface
{

    /**
     * @param string ...$ids
     * @return ResourceObjectInterface[]
     */
    public function havingIds(string ...$ids): array;

}