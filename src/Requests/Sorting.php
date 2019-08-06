<?php


namespace JsonApiPresenter\Requests;


use JsonApiPresenter\Exceptions\InvalidArgumentException;

class Sorting
{
    const DIRECTION_ASC = 'asc';
    const DIRECTION_DESC = 'desc';

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $direction;

    /**
     * Sorting constructor.
     * @param string $field
     * @param string $direction
     * @throws InvalidArgumentException
     */
    public function __construct(string $field, string $direction = self::DIRECTION_DESC)
    {
        if (!\in_array($direction, [self::DIRECTION_ASC, self::DIRECTION_DESC])) {
            throw new InvalidArgumentException(\sprintf('Invalid direction %s', $direction));
        }

        $this->field = $field;
        $this->direction = $direction;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getDirection(): string
    {
        return $this->direction;
    }

}