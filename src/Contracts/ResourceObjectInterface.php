<?php

declare(strict_types=1);

namespace JsonApiPresenter\Contracts;


use JsonApiPresenter\Attributes;
use JsonApiPresenter\ResourceLinks;
use JsonApiPresenter\Meta;
use JsonApiPresenter\RelationshipsCollection;
use JsonApiPresenter\ResourceIdentifier;

interface ResourceObjectInterface extends Arrayable
{

    /**
     * @return ResourceIdentifier
     */
    public function getIdentifier(): ResourceIdentifier;

    /**
     * @return Attributes
     */
    public function getAttributes(): Attributes;

    /**
     * @return RelationshipsCollection
     */
    public function getRelationships(): RelationshipsCollection;

    /**
     * @return ResourceLinks|null
     */
    public function getLinks();

    /**
     * @return Meta|null
     */
    public function getMeta();

}