<?php


namespace JsonApiPresenter\Contracts;


interface Relationship extends Arrayable
{

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return bool
     */
    public function isEmpty(): bool;

}