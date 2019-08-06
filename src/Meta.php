<?php


namespace JsonApiPresenter;


use JsonApiPresenter\Contracts\Arrayable;

final class Meta implements Arrayable
{

    /**
     * @var array
     */
    private $meta;

    /**
     * Meta constructor.
     * @param array $meta
     */
    public function __construct(array $meta = [])
    {
        $this->meta = $meta;
    }

    /**
     * @param Meta|null $meta
     * @return Meta
     */
    public function merge(Meta $meta = null): Meta
    {
        $merge = null === $meta ? [] : $meta->toArray();

        return new Meta(\array_merge(
            $this->toArray(),
            $merge
        ));
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->meta;
    }
}