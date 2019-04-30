<?php

declare(strict_types=1);

namespace JsonApiPresenter;


use JsonApiPresenter\Contracts\Arrayable;
use JsonApiPresenter\Exceptions\InvalidArgumentException;

final class ResourceIdentifier implements Arrayable
{

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * ResourceIdentifier constructor.
     * @param string $id
     * @param string $type
     * @throws InvalidArgumentException
     */
    public function __construct(string $id, string $type)
    {
        if (empty($id)) {
            throw new InvalidArgumentException('Invalid resource id');
        }

        if (empty($type)) {
            throw new InvalidArgumentException('Invalid resource type');
        }

        $this->id = $id;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param ResourceIdentifier|null $identifier
     * @return bool
     */
    public function equals(ResourceIdentifier $identifier = null): bool
    {
        return
            null !== $identifier &&
            $this->getId() === $identifier->getId() &&
            $this->getType() === $identifier->getType();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'type' => $this->getType()
        ];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \sprintf('%s - %s', $this->getId(), $this->getType());
    }

}