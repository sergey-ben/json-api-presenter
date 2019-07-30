<?php


namespace JsonApiPresenter;


use JsonApiPresenter\Contracts\Arrayable;

class PaginationLinks implements Arrayable
{

    /**
     * @var Link
     */
    private $first;

    /**
     * @var Link
     */
    private $last;

    /**
     * @var Link|null
     */
    private $prev;

    /**
     * @var Link|null
     */
    private $next;

    /**
     * PaginationLinks constructor.
     * @param Link $first
     * @param Link $last
     * @param Link|null $prev
     * @param Link|null $next
     */
    public function __construct(Link $first, Link $last, Link $prev = null, Link $next = null)
    {
        $this->first = $first;
        $this->last = $last;
        $this->prev = $prev;
        $this->next = $next;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'first' => $this->first->toArray(),
            'last' => $this->last->toArray(),
            'prev' => null === $this->prev ? null : $this->prev->toArray(),
            'next' => null === $this->next ? null : $this->next->toArray(),
        ];
    }
}