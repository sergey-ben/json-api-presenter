<?php


namespace JsonApiPresenter;


use JsonApiPresenter\Contracts\Arrayable;

class Link implements Arrayable
{

    /**
     * @var string
     */
    private $href;

    /**
     * @var Meta
     */
    private $meta;

    /**
     * Link constructor.
     * @param string $href
     * @param Meta|null $meta
     */
    public function __construct(string $href, Meta $meta = null)
    {
        $this->href = $href;
        $this->meta = $meta;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $result = [
            'href' => $this->href
        ];

        if (null !== $this->meta) {
            $result['meta'] = $this->meta->toArray();
        }

        return $result;
    }
}