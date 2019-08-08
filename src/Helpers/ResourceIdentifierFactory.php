<?php


namespace JsonApiPresenter\Helpers;


use JsonApiPresenter\ResourceIdentifier;

class ResourceIdentifierFactory
{

    /**
     * @param string $type
     * @param string ...$ids
     * @return ResourceIdentifier[]
     */
    public static function collection(string $type, string ...$ids): array
    {
        return \array_map(function (string $id) use ($type) {
            return new ResourceIdentifier($id, $type);
        }, $ids);
    }

}