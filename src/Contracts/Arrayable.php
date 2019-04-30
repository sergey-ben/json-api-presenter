<?php


namespace JsonApiPresenter\Contracts;


interface Arrayable
{

    /**
     * @return array
     */
    public function toArray(): array;

}