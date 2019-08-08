<?php


use JsonApiPresenter\Attributes;
use JsonApiPresenter\Contracts\DataSourceInterface;
use JsonApiPresenter\Link;
use JsonApiPresenter\ResourceRelationships;
use JsonApiPresenter\ResourceIdentifier;
use JsonApiPresenter\ResourceLinks;
use JsonApiPresenter\ResourceObject;
use JsonApiPresenter\ToOneRelationship;

class BooksDataSource implements DataSourceInterface
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
                new ResourceIdentifier(1, 'books'),
                new Attributes([
                    'title' => 'Book a'
                ]),
                new ResourceRelationships(
                    new ToOneRelationship(
                        'author',
                        new ResourceIdentifier(1, 'people')
                    )
                ),
                new ResourceLinks(
                    new Link('/books/1')
                )
            ),
            2 => new ResourceObject(
                new ResourceIdentifier(2, 'books'),
                new Attributes([
                    'title' => 'Book b'
                ]),
                new ResourceRelationships(
                    new ToOneRelationship(
                        'author',
                        new ResourceIdentifier(1, 'people')
                    )
                ),
                new ResourceLinks(
                    new Link('/books/2')
                )
            ),
            3 => new ResourceObject(
                new ResourceIdentifier(3, 'books'),
                new Attributes([
                    'title' => 'Book c'
                ]),
                new ResourceRelationships(
                    new ToOneRelationship(
                        'author',
                        new ResourceIdentifier(2, 'people')
                    )
                ),
                new ResourceLinks(
                    new Link('/books/3')
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