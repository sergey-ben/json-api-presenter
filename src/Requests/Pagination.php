<?php


namespace JsonApiPresenter\Requests;


class Pagination
{

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $offset;

    public function __construct(int $limit = null, int $offset = null)
    {
        $this->limit = $limit;
        $this->offset = $offset;
    }

    /**
     * @return int|null
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return int|null
     */
    public function getOffset()
    {
        return $this->offset;
    }

}