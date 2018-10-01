<?php

namespace Antiflood\Telegram\Methods;

/**
 * Class GetUpdates
 *
 * @package Antiflood\Telegram\Methods
 */
class GetUpdates extends AbstractMethod
{
    /** @var string */
    private const METHOD_NAME = 'getUpdates';
    /** @var int */
    private const DEFAULT_LIMIT = 1;

    /** @var int **/
    private $limit;
    /** @var int */
    private $lastId = 0;

    /**
     * GetUpdates constructor.
     *
     * @param int $lastId
     * @param int $limit
     */
    public function __construct(int $lastId = 0, int $limit = self::DEFAULT_LIMIT)
    {
        $this->limit = $limit;
        $this->lastId = $lastId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::METHOD_NAME;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return [
            'offset' => $this->lastId + 1,
            'limit' => $this->limit,
        ];
    }
}
