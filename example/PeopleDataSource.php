<?php


use JsonApiPresenter\Attributes;
use JsonApiPresenter\Contracts\DataSourceInterface;
use JsonApiPresenter\Link;
use JsonApiPresenter\RelationshipsCollection;
use JsonApiPresenter\ResourceIdentifier;
use JsonApiPresenter\ResourceLinks;
use JsonApiPresenter\ResourceObject;
use JsonApiPresenter\ToManyRelationship;

class PeopleDataSource implements DataSourceInterface
{
    private $map;

    /**
     * BooksDataSource constructor.
     * @throws \JsonApiPresenter\Exceptions\InvalidArgumentException
     */
    public function __construct()
    {
        $this->map = [
            1 => new ResourceObject(
                new ResourceIdentifier(1, 'people'),
                new Attributes([
                    'name' => 'Vasya'
                ]),
                new RelationshipsCollection(
                    new ToManyRelationship(
                        'books',
                        [
                            new ResourceIdentifier(1, 'books'),
                            new ResourceIdentifier(2, 'books')
                        ]
                    )
                ),
                new ResourceLinks(
                    new Link('/people/1')
                )
            ),
            2 => new ResourceObject(
                new ResourceIdentifier(2, 'people'),
                new Attributes([
                    'name' => 'Petya'
                ]),
                new RelationshipsCollection(
                    new ToManyRelationship(
                        'books',
                        [
                            new ResourceIdentifier(3, 'books')
                        ]
                    )
                ),
                new ResourceLinks(
                    new Link('/people/2')
                )
            ),
        ];
    }

    /**
     * @param string[] $ids
     * @return ResourceObject[]
     */
    public function havingIds(string ...$ids): array
    {
        return array_filter($this->map, function(ResourceObject $resource) use ($ids) {
            return in_array($resource->getIdentifier()->getId(), $ids);
        });
    }
}