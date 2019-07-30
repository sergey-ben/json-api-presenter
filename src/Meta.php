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
     * @return array
     */
    public function toArray(): array
    {
        return $this->meta;
    }
}