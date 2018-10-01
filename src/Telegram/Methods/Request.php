<?php

namespace Antiflood\Telegram\Methods;

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
     * @return array
     */
    public function getParams(): array;
}
