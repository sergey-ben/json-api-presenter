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

    /**
     * Pagination constructor.
     * @param int|null $limit
     * @param int|null $offset
     */
    public function __construct(int $limit = null, int $offset = null)
    {
        $this->limit = $limit;
        $this->offset = $offset;
    }

    /**
     * @return Pagination
     */
    public static function empty(): Pagination
    {
        return new self();
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