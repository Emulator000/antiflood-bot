<?php

namespace Antiflood\Telegram\Methods;

use Antiflood\Telegram\Update;

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
    private $lastSavedId = [];

    /**
     * GetUpdates constructor.
     *
     * @param int $limit
     */
    public function __construct(int $limit = self::DEFAULT_LIMIT)
    {
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
     * @param int $botIndex
     *
     * @return array
     */
    public function getParams(int $botIndex = 0): array
    {
        return [
            'offset' => $this->lastSavedId[$botIndex] ?? 0,
            'limit' => $this->limit,
        ];
    }

    /**
     * @param Update[] $updates
     * @param int $botIndex
     */
    public function handleUpdates(array $updates, int $botIndex = 0): void
    {
        if (false === empty($updates)) {
            $this->lastSavedId[$botIndex] = end($updates)->getId() + 1;
        }
    }
}
