<?php


namespace JsonApiPresenter\Requests;


class Fieldset
{

    /**
     * @var string
     */
    private $type;

    /**
     * @var array|string[]
     */
    private $fields;

    /**
     * Fieldset constructor.
     * @param string $type
     * @param string ...$fields
     */
    public function __construct(string $type, string ...$fields)
    {
        $this->type = $type;
        $this->fields = $fields;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array|string[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->fields);
    }

    /**
     * @param string $field
     * @return bool
     */
    public function hasField(string $field): bool
    {
        return \in_array($field, $this->fields);
    }

}