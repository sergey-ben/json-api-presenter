<?php


namespace JsonApiPresenter;


use JsonApiPresenter\Contracts\Arrayable;

final class JsonApi implements Arrayable
{
    const VERSION_1_0 = '1.0';
    const VERSION_1_1 = '1.1';

    /**
     * @var string
     */
    private $version;

    /**
     * @var Meta|null
     */
    private $meta;

    /**
     * JsonApi constructor.
     * @param string $version
     * @param Meta|null $meta
     */
    public function __construct(string $version = self::VERSION_1_1, Meta $meta = null)
    {
        $this->version = $version;
        $this->meta = $meta;
    }

    /**
     * @return JsonApi
     */
    public static function default(): JsonApi
    {
        return new self();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $result = [
            'version' => $this->version
        ];

        if (null !== $this->meta) {
            $result['meta'] = $this->meta->toArray();
        }

        return $result;
    }
}