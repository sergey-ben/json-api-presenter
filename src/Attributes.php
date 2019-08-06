<?php

declare(strict_types=1);

namespace JsonApiPresenter;


use JsonApiPresenter\Contracts\Arrayable;
use JsonApiPresenter\Exceptions\InvalidArgumentException;
use JsonApiPresenter\Requests\Fieldset;

final class Attributes implements Arrayable
{

    /**
     * @var array
     */
    private $attributes;

    /**
     * @var Fieldset
     */
    private $fieldset;

    /**
     * Attributes constructor.
     * @param array $attributes
     * @param Fieldset|null $fieldset
     * @throws InvalidArgumentException
     */
    public function __construct(array $attributes = [], Fieldset $fieldset = null)
    {
        if (isset($attributes['id'])) {
            throw new InvalidArgumentException('Attributes can\'t have attribute named `id`');
        }

        if (isset($attributes['type'])) {
            throw new InvalidArgumentException('Attributes can\'t have attribute named `type`');
        }

        $this->attributes = $attributes;
        $this->fieldset = $fieldset;
    }

    /**
     * @param Fieldset $fieldset
     */
    public function addFieldset(Fieldset $fieldset)
    {
        $this->fieldset = $fieldset;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasAttribute(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * @param string $name
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getAttribute(string $name)
    {
        if (!$this->hasAttribute($name)) {
            throw new InvalidArgumentException('Invalid attribute name');
        }

        return $this->attributes[$name];
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->attributes);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $attributes = $this->attributes;

        if (null !== $this->fieldset) {
            $attributes = \array_filter($attributes, function (string $field) {
                return $this->fieldset->hasField($field);
            }, ARRAY_FILTER_USE_KEY);
        }

        return $attributes;
    }
}