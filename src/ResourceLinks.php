<?php


namespace JsonApiPresenter;


use JsonApiPresenter\Contracts\Arrayable;

final class ResourceLinks implements Arrayable
{
    /**
     * @var Link|null
     */
    private $self;

    /**
     * @var Link|null
     */
    private $related;

    /**
     * Links constructor.
     * @param Link|null $self
     * @param Link|null $related
     */
    public function __construct(Link $self = null, Link $related = null)
    {
        $this->self = $self;
        $this->related = $related;
    }

    /**
     * @param string $href
     * @param Meta|null $meta
     */
    public function addSelfLink(string $href, Meta $meta = null)
    {
        $this->self = new Link($href, $meta);
    }

    /**
     * @param string $href
     * @param Meta|null $meta
     */
    public function addRelatedLink(string $href, Meta $meta = null)
    {
        $this->related = new Link($href, $meta);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $result = [];

        if (null !== $this->self) {
            $result['self'] = $this->self->toArray();
        }

        if (null !== $this->related) {
            $result['related'] = $this->related->toArray();
        }

        return $result;
    }
}