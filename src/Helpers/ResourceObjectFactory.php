<?php


namespace JsonApiPresenter\Helpers;


use JsonApiPresenter\Attributes;
use JsonApiPresenter\Exceptions\RuntimeException;
use JsonApiPresenter\ResourceIdentifier;
use JsonApiPresenter\ResourceObject;
use JsonApiPresenter\ResourceObjectsCollection;

class ResourceObjectFactory
{

    /**
     * @param array $data
     * @param string $type
     * @param callable|null $formatAttributes
     * @return ResourceObjectsCollection
     */
    public static function collection(array $data, string $type, callable $formatAttributes = null): ResourceObjectsCollection
    {
        $resources = \array_map(function (array $item) use ($type, $formatAttributes) {
            if (!isset($item['id'])) {
                throw new RuntimeException('Unable to create resource object without id');
            }

            $id = $item['id'];
            unset($item['id']);

            if (null !== $formatAttributes) {
                $formatAttributes($item);
            }

            return new ResourceObject(
                new ResourceIdentifier($id, $type),
                new Attributes($item)
            );
        }, $data);

        return new ResourceObjectsCollection(...$resources);
    }

}