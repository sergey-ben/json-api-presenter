<?php


namespace JsonApiPresenter\Contracts;


use JsonApiPresenter\ResourceObjectsCollection;

interface DefinesRelationshipsDataSource
{

    /**
     * @param ResourceObjectsCollection $resources
     * @return void
     */
    public function defineRelationships(ResourceObjectsCollection $resources);

}