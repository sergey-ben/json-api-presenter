<?php


namespace JsonApiPresenter\Contracts;


use JsonApiPresenter\ResourceObject;

interface AppliesRelationshipsDataSourceInterface
{

    /**
     * @param ResourceObject ...$resources
     * @return void
     */
    public function applyRelationships(ResourceObject ...$resources);

}