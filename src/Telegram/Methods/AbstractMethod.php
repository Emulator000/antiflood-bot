<?php

namespace Antiflood\Telegram\Methods;

/**
 * Class AbstractMethod
 *
 * @package Antiflood\Telegram\Methods
 */
abstract class AbstractMethod implements Request
{
    protected const DEFAULT_METHOD = 'POST';

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return self::DEFAULT_METHOD;
    }

    /**
     * @return bool
     */
    public function isAsync(): bool
    {
        return true;
    }
}
