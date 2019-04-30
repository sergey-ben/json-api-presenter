<?php

declare(strict_types=1);

namespace JsonApiPresenter;


use JsonApiPresenter\Contracts\Arrayable;
use JsonApiPresenter\Exceptions\InvalidArgumentException;

final class Attributes implements Arrayable
{

    /**
     * @var array
     */
    private $attributes;

    /**
     * Attributes constructor.
     * @param array $attributes
     * @throws InvalidArgumentException
     */
    public function __construct(array $attributes = [])
    {
        if (isset($attributes['id'])) {
            throw new InvalidArgumentException('Attributes can\'t have attribute named `id`');
        }

        if (isset($attributes['type'])) {
            throw new InvalidArgumentException('Attributes can\'t have attribute named `type`');
        }

        $this->attributes = $attributes;
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
        return $this->attributes;
    }
}