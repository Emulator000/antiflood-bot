<?php

namespace Antiflood\Telegram\Methods;

use Antiflood\Telegram\Update;

/**
 * Interface Request
 *
 * @package Antiflood\Telegram\Methods
 */
interface Request
{
    /**
     * @return string
     */
    public function getMethod(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param int $botIndex
     *
     * @return array
     */
    public function getParams(int $botIndex = 0): array;

    /**
     * @param Update[] $updates
     * @param int $botIndex
     */
    public function handleUpdates(array $updates, int $botIndex = 0): void;
}
