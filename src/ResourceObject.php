<?php


namespace JsonApiPresenter;


use JsonApiPresenter\Contracts\Arrayable;
use JsonApiPresenter\Requests\Fieldset;

class ResourceObject implements Arrayable
{

    /**
     * @var ResourceIdentifier
     */
    private $identifier;

    /**
     * @var Attributes|null
     */
    private $attributes;

    /**
     * @var RelationshipsCollection
     */
    private $relationships;

    /**
     * @var ResourceLinks
     */
    private $links;

    /**
     * @var Meta|null
     */
    private $meta;

    /**
     * ResourceObject constructor.
     * @param ResourceIdentifier $identifier
     * @param Attributes|null $attributes
     * @param RelationshipsCollection|null $relationships
     * @param ResourceLinks|null $links
     * @param Meta|null $meta
     * @throws Exceptions\InvalidArgumentException
     */
    public function __construct(
        ResourceIdentifier $identifier,
        Attributes $attributes = null,
        RelationshipsCollection $relationships = null,
        ResourceLinks $links = null,
        Meta $meta = null
    ) {
        if (null === $attributes) {
            $attributes = new Attributes();
        }

        if (null === $relationships) {
            $relationships = new RelationshipsCollection();
        }

        $this->identifier = $identifier;
        $this->attributes = $attributes;
        $this->relationships = $relationships;
        $this->links = $links;
        $this->meta = $meta;
    }

    /**
     * @param string $href
     * @param Meta|null $meta
     */
    public function addSelfLink(string $href, Meta $meta = null)
    {
        if (null === $this->links) {
            $this->links = new ResourceLinks(new Link(
                $href,
                $meta
            ));

            return ;
        }

        $this->links->addSelfLink($href, $meta);
    }

    /**
     * @param string $name
     * @param ResourceIdentifier|null $data
     * @param ResourceLinks|null $links
     * @param Meta|null $meta
     * @throws Exceptions\InvalidArgumentException
     */
    public function addToOneRelationship(
        string $name,
        ResourceIdentifier $data = null,
        ResourceLinks $links = null,
        Meta $meta = null
    ) {
        $this->relationships->addToOneRelationship($name, $data, $links, $meta);
    }

    /**
     * @param string $name
     * @param ResourceIdentifier[] $data
     * @param ResourceLinks|null $links
     * @param Meta|null $meta
     * @throws Exceptions\InvalidArgumentException
     */
    public function addToManyRelationship(
        string $name,
        array $data = [],
        ResourceLinks $links = null,
        Meta $meta = null
    ) {
        $this->relationships->addToManyRelationship($name, $data, $links, $meta);
    }

    /**
     * @param Fieldset $fieldset
     */
    public function addFieldset(Fieldset $fieldset)
    {
        $this->attributes->addFieldset($fieldset);
    }

    /**
     * @return ResourceIdentifier
     */
    public function getIdentifier(): ResourceIdentifier
    {
        return $this->identifier;
    }

    /**
     * @return Attributes
     */
    public function getAttributes(): Attributes
    {
        return $this->attributes;
    }

    /**
     * @return RelationshipsCollection
     */
    public function getRelationships(): RelationshipsCollection
    {
        return $this->relationships;
    }

    /**
     * @return ResourceLinks|null
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @return Meta|null
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $result = $this->getIdentifier()->toArray();

        if (!$this->getAttributes()->isEmpty()) {
            $result['attributes'] = $this->getAttributes()->toArray();
        }

        if (!$this->getRelationships()->isEmpty()) {
            $result['relationships'] = $this->getRelationships()->toArray();
        }

        if (null !== $this->getLinks()) {
            $result['links'] = $this->getLinks()->toArray();
        }

        if (null !== $this->getMeta()) {
            $result['meta'] = $this->getMeta()->toArray();
        }

        return $result;
    }

}