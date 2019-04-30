<?php


namespace JsonApiPresenter\Contracts;


interface ResourceRepositoryInterface
{

    /**
     * @param string[] $ids
     * @return ResourceObjectInterface[]
     */
    public function havingIds(string ...$ids): array;

}