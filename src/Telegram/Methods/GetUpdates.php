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
    private $lastId;
    /** @var int **/
    private $limit;

    /**
     * GetUpdates constructor.
     *
     * @param int $lastId
     * @param int $limit
     */
    public function __construct(int $lastId, int $limit = self::DEFAULT_LIMIT)
    {
        $this->lastId = $lastId;
        $this->limit = $limit;
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
            'offset' => $this->lastId,
            'limit' => $this->limit,
        ];
    }
}
